<?php

use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Opportunity\Category;
use HnutiBrontosaurus\BisClient\Opportunity\Request\OpportunityParameters;
use HnutiBrontosaurus\BisClient\Opportunity\Response\Opportunity;
use HnutiBrontosaurus\Theme\CannotResolveCoordinates;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\DataContainers\Structure\AdministrationUnit;
use Nette\Utils\Strings;

/** @var Container $hb_container defined in functions.php */

$hb_bisApiClient = $hb_container->getBisClient();
$hb_coordinatesResolver = $hb_container->getCoordinatesResolver();
$hb_dateFormatHuman = $hb_container->getDateFormatForHuman();
$hb_dateFormatRobot = $hb_container->getDateFormatForRobot();

// todo: use some WP way of obtaining param
$selectedFilter = filter_input( INPUT_GET, 'jen' ) ?? null;
$selectedFilter = $selectedFilter !== null && $selectedFilter !== '' ? htmlspecialchars($selectedFilter) : null;

$isOrganizovaniAkciSelected = $selectedFilter === 'organizovani-akci';
$isSpolupraceSelected = $selectedFilter === 'spoluprace';
$isPomocLokaliteSelected = $selectedFilter === 'pomoc-lokalite';
$isAnySelected = $isOrganizovaniAkciSelected || $isSpolupraceSelected || $isPomocLokaliteSelected;

$applyFilter = static function (?string $filter): ?OpportunityParameters {
	if ($filter === null) return null;

	$params = new OpportunityParameters();
	$category = match ($filter) {
		'organizovani-akci' => Category::ORGANIZING(),
		'spoluprace' => Category::COLLABORATION(),
		'pomoc-lokalite' => Category::LOCATION_HELP(),
	};
	$params->setCategory($category);
	return $params;
};

$hasBeenUnableToLoad = false;
$administrationUnits = [];

try {
	$opportunities = $hb_bisApiClient->getOpportunities($applyFilter($selectedFilter));

	foreach ($hb_bisApiClient->getAdministrationUnits() as $administrationUnit) {
		try {
			$administrationUnits[] = AdministrationUnit::from($administrationUnit, $hb_coordinatesResolver);

		} catch (CannotResolveCoordinates) {
			continue; // in case of non-existing address just silently continue and ignore this unit
		}
	}

} catch (ConnectionToBisFailed) {
	$hasBeenUnableToLoad = true;
}

$administrationUnitsInJson = json_encode($administrationUnits);

$numberOfOpportunitiesToDisplayOnLoad = 6;
?>

<?php function hb_opportunity(Opportunity $opportunity, string $dateFormatForHuman, string $dateFormatForRobot) { ?>
	<a class="hb-event" href="prilezitost/<?php echo $opportunity->getId(); //todo: using rather WP routing somehow ?>">
		<div class="hb-event__imageWrapper">
			<img alt="" class="hb-event__image" data-src="<?php echo $opportunity->getImage()->getMediumSizePath(); ?>">
			<noscript><?php // for search engines ?>
				<img alt="" class="hb-event__image" src="<?php echo $opportunity->getImage()->getMediumSizePath(); ?>">
			</noscript>

			<div class="hb-event__labels hb-eventLabels">
				<div class="hb-eventLabels__item">
					<?php echo hb_opportunityCategoryToString($opportunity->getCategory()); ?>
				</div>
			</div>
		</div>

		<header class="hb-event__header">
			<h4 class="hb-event__heading">
				<?php echo $opportunity->getName(); ?>
			</h4>

			<div class="hb-event__meta">
				<time class="hb-event__date" datetime="<?php echo $opportunity->getStartDate()->toDateTimeImmutable()->format($dateFormatForRobot) ?>">
					<?php echo hb_dateSpan($opportunity->getStartDate(), $opportunity->getEndDate(), $dateFormatForHuman); ?>
				</time>

				<span class="hb-event__place" title="Místo konání">
					<?php echo $opportunity->getLocation()->getName(); ?>
				</span>
			</div>
		</header>

		<div class="hb-event__excerpt">
			<?php
				$text = (string) $opportunity->getIntroduction();
				$text = hb_strip_tags($text);
				$text = Strings::truncate($text, 200);
				$text = nl2br($text);
				echo $text;
			?>
		</div>
	</a>
<?php } ?>

<main class="hb-pbe-6" role="main" id="obsah">
	<section>
		<h1>Aktuální příležitosti</h1>

		<div class="filters hb-expandable hb-mbe-4" <?php if ($isAnySelected): ?> data-hb-expandable-expanded="1"<?php endif; ?>>
			<button class="hb-expandable__toggler button button--customization" type="button" aria-hidden="true" data-hb-expandable-toggler>
				Zobrazit pouze
			</button>

			<ul class="filters__list" id="events-filters-list" data-hb-expandable-content>
				<li class="filters__item">
					<a class="filters__link <?php echo ! $isAnySelected ? 'filters__link--selected' : ''; ?> button button--customization" href="?#obsah">
						vše
					</a>
				</li>

				<li class="filters__item">
					<a class="filters__link <?php echo $isOrganizovaniAkciSelected ? 'filters__link--selected' : ''; ?> button button--customization" href="?jen=organizovani-akci#obsah">
						organizování akcí
					</a>
				</li>

				<li class="filters__item">
					<a class="filters__link <?php echo $isSpolupraceSelected ? 'filters__link--selected' : ''; ?> button button--customization" href="?jen=spoluprace#obsah">
						spolupráce
					</a>
				</li>

				<li class="filters__item">
					<a class="filters__link <?php echo $isPomocLokaliteSelected ? 'filters__link--selected' : ''; ?> button button--customization" href="?jen=pomoc-lokalite#obsah">
						pomoc lokalitě
					</a>
				</li>
			</ul>
		</div>

		<div class="hb-eventList hb-mbe-7">
			<?php if ($hasBeenUnableToLoad): ?>
				<div class="hb-eventList__noResults noResults">
					Promiň, zrovna nám vypadl systém, kde máme uloženy všechny informace o příležitostech.
					Zkus to prosím za chvilku znovu.
				</div>

			<?php elseif (count($opportunities) === 0): ?>
				<div class="hb-eventList__noResults noResults">
					Zrovna tu žádné příležitosti nejsou, zkus to později.
				</div>

			<?php else: ?>
				<div class="hb-eventList__grid">
					<?php $i = 1; foreach ($opportunities as $opportunity) {
						hb_opportunity($opportunity, $hb_dateFormatHuman, $hb_dateFormatRobot);
						if ($i === $numberOfOpportunitiesToDisplayOnLoad) break;
						$i++;
					} ?>
				</div>

				<?php if (count($opportunities) > $numberOfOpportunitiesToDisplayOnLoad): ?>
					<div class="hb-expandable">
						<button class="hb-eventList__moreLink button button--customization" type="button" data-hb-expandable-toggler data-hb-expandable-toggler-remove-on-expand>
							Zobrazit další
						</button>

						<div class="hb-eventList__grid hb-eventList__grid--collapse" data-hb-expandable-content>
							<?php $i = 1; foreach ($opportunities as $opportunity) {
								if ($i <= $numberOfOpportunitiesToDisplayOnLoad) {
									$i++;
									continue;
								}
								hb_opportunity($opportunity, $hb_dateFormatHuman, $hb_dateFormatRobot);
							} ?>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>


		<h1>Zapoj se do Brontosaura</h1>

		<div class="description">
			<div class="description__list">
				<a class="description__item hb-optionBox button--secondary-wrapper" href="/zapoj-se/organizovani">
					<div class="description__itemText hb-optionBox__text">
						<h2>
							Chci organizovat akce
						</h2>

						<p class="hb-optionBox__description">
							Zapoj se do organizování akcí, naber zkušenosti
							na organizátorských kurzech nebo získej podporu v podobě
							mentoringu, propagace, financí, online nástrojů či výběru
							správné lokality.
						</p>
					</div>

					<div class="hb-optionBox__image" style="--hb-optionBox-image: url('https://brontosaurus.cz/wp-content/uploads/2024/12/zapoj-se-organizovani-scaled.jpg');">
						<div class="button button--secondary">Více info</div>
					</div>
				</a>

				<a class="description__item hb-optionBox button--secondary-wrapper" href="/zapoj-se/spoluprace">
					<div class="description__itemText hb-optionBox__text">
						<h2>
							Chci spolupracovat
						</h2>

						<p class="hb-optionBox__description">
							Začni s námi spolupracovat na péči o přírodu,
							pořádání Ekostanů, činnosti dětských oddílů či organizaci soutěží.
							Zapoj se jako dobrovolník, lektor či patron budkování - dle tvých možností.
						</p>
					</div>

					<div class="hb-optionBox__image" style="--hb-optionBox-image: url('https://brontosaurus.cz/wp-content/uploads/2024/12/zapoj-se-spoluprace-scaled.jpg'); --hb-optionBox-y-offset: bottom;">
						<div class="button button--secondary">Více info</div>
					</div>
				</a>

				<a class="description__item hb-optionBox button--secondary-wrapper" href="/zapoj-se/clenstvi">
					<div class="description__itemText hb-optionBox__text">
						<h2>
							Chci se stát členem
						</h2>

						<p class="hb-optionBox__description">
							Členství v Hnutí Brontosaurus ti přinese spoustu skvělých výhod a zároveň podpoříš naši činnost a péči o přírodu a památky.
						</p>
					</div>

					<div class="hb-optionBox__image" style="--hb-optionBox-image: url('https://brontosaurus.cz/wp-content/uploads/2024/12/zapoj-se-clenstvi.jpg');">
						<div class="button button--secondary">Více info</div>
					</div>
				</a>

				<a class="description__item hb-optionBox button--secondary-wrapper" href="https://mozek.brontosaurus.cz/index.php/cely-mozek/provoz-clanku-klubu-rc/zakladame-zc-rc-klub" rel="noopener" target="_blank">
					<div class="description__itemText hb-optionBox__text">
						<h2>
							Chci založit článek, klub či oddíl
						</h2>

						<p class="hb-optionBox__description">
							Našim základním článkům, klubům a dětským oddílům zajišťujeme finanční a marketingovou podporu, pojištění, slevy a mnoho dalších výhod.
						</p>
					</div>

					<div class="hb-optionBox__image" style="--hb-optionBox-image: url('https://brontosaurus.cz/wp-content/uploads/2024/12/zapoj-se-zalozeni-clanku-scaled.jpg');">
						<div class="button button--secondary">Více info</div>
					</div>
				</a>
			</div>
		</div>

		<div class="hb-mbs-6 hb-mw-40 hb-mi-auto hb-mbe-5">
			<h2 class="hb-mbe-4">Chceš se zapojit? Ozvi se nám!</h2>

			<?php hb_administrative_units_map($administrationUnitsInJson, $hasBeenUnableToLoad) ?>
		</div>

		<div class="hb-block-text">
			<h2>Kontakty na lokální koordinátory</h2>

			<ul class="hb-ta-c" style="list-style-type: none">
				<li><strong>Praha:</strong>
					Jakub Hájek,
					<a href="tel:+420728344633" rel="noopener noreferrer" target="_blank">728 344 633</a>,
					<a href="mailto:praha@brontosaurus.cz" rel="noopener noreferrer" target="_blank">praha@brontosaurus.cz</a>
				</li>

				<li><strong>Brno:</strong>
					Hana Rosová,
					<a href="tel:+420734392735" rel="noopener noreferrer" target="_blank">734 392 735</a>,
					<a href="mailto:hnuti@brontosaurus.cz" rel="noopener noreferrer" target="_blank">hnuti@brontosaurus.cz</a>
				</li>

				<li><strong>Podluží:</strong>
					Dalimil Toman,
					<a href="tel:+420605763112" rel="noopener noreferrer" target="_blank">605 763 112</a>,
					<a href="mailto:podluzi@brontosaurus.cz" rel="noopener noreferrer" target="_blank">podluzi@brontosaurus.cz</a>
				</li>
			</ul>
		</div>
	</section>
</main>

<aside>
	<section class="related">
		<h1 class="related-heading">
			Ještě by tě mohlo zajímat
		</h1>

		<div class="related-list">
			<a class="related-list-item related-list-item--events button--secondary-wrapper" href="/co-se-chysta">
				<h2 class="related-list-item-heading">
					Kalendář akcí
				</h2>

				<ul class="related-list-item-list">
					<li>
						Dobrovolnické akce
					</li>

					<li>
						Kurzy a přednášky
					</li>

					<li>
						Setkávání a kluby
					</li>
				</ul>

				<div class="related-list-item-roundedPhoto related-list-item--events-roundedPhoto"></div>
				<div class="related-list-item-moreLink button button--secondary">Chci více info</div>
			</a>

			<a class="related-list-item related-list-item--mozek button--secondary-wrapper" href="https://mozek.brontosaurus.cz" rel="noopener" target="_blank">
				<h2 class="related-list-item-heading">
					Mozek pro organizátory
				</h2>

				<ul class="related-list-item-list">
					<li>
						Podpora organizování akcí
					</li>

					<li>
						Dokumenty, manuály a šablony
					</li>

					<li>
						Kontakty a odkazy
					</li>
				</ul>

				<div class="related-list-item-roundedPhoto related-list-item--mozek-roundedPhoto"></div>
				<div class="related-list-item-moreLink button button--secondary">Chci více info</div>
			</a>

			<a class="related-list-item related-list-item--podpor-nas button--secondary-wrapper" href="/podpor-nas">
				<h2 class="related-list-item-heading">
					Podpoř nás
				</h2>

				<ul class="related-list-item-list">
					<li>
						Podpora našich projektů
					</li>

					<li>
						Adopce Brontosaura
					</li>

					<li>
						E-shop s dárky přírodě
					</li>
				</ul>

				<div class="related-list-item-roundedPhoto related-list-item--podpor-nas-roundedPhoto"></div>
				<div class="related-list-item-moreLink button button--secondary">Chci více info</div>
			</a>

			<a class="related-list-item related-list-item--eshop button--secondary-wrapper" href="https://eshop.brontosaurus.cz" rel="noopener" target="_blank">
				<h2 class="related-list-item-heading">
					E-shop
				</h2>

				<ul class="related-list-item-list">
					<li>
						Benefiční předměty
					</li>

					<li>
						Brontosauří trička a tašky
					</li>

					<li>
						Publikace a metodiky
					</li>
				</ul>

				<div class="related-list-item-roundedPhoto related-list-item--eshop-roundedPhoto"></div>
				<div class="related-list-item-moreLink button button--secondary">Chci více info</div>
			</a>
		</div>
	</section>
</aside>
