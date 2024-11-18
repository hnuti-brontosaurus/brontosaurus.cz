<?php declare(strict_types = 1);

use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Opportunity\Category;
use HnutiBrontosaurus\BisClient\Opportunity\Request\OpportunityParameters;
use HnutiBrontosaurus\BisClient\Opportunity\Response\Opportunity;
use HnutiBrontosaurus\Theme\CannotResolveCoordinates;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\UI\DataContainers\Structure\AdministrationUnit;
use Nette\Utils\Strings;
use function HnutiBrontosaurus\Theme\hb_dateSpan;
use function HnutiBrontosaurus\Theme\hb_opportunityCategoryToString;


/** @var Container $hb_container defined in functions.php */

$hb_bisApiClient = $hb_container->getBisClient();
$hb_dateFormat = $hb_container->getDateFormatForHuman();
$hb_latte = $hb_container->getLatte();
$hb_coordinatesResolver = $hb_container->getCoordinatesResolver();


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

add_action('wp_enqueue_scripts', function () {
	$theme = wp_get_theme();
	$themeVersion = $theme->get('Version');
	wp_enqueue_script('brontosaurus-zapojse-lazy-load', $theme->get_template_directory_uri() . '/frontend/dist/js/lazyLoad.js', [], $themeVersion);
	wp_enqueue_script('brontosaurus-zapojse-map', $theme->get_template_directory_uri() . '/frontend/dist/js/administrativeUnitsMap.js', [], $themeVersion);
});

$params = [
	'themePath' => get_template_directory_uri(),
	'administrationUnitsInJson' => \json_encode($administrationUnits),
	'hasBeenUnableToLoad' => $hasBeenUnableToLoad,
];

$numberOfOpportunitiesToDisplayOnLoad = 6;
?>

<?php function hb_opportunity(Opportunity $opportunity, string $dateFormat) { ?>
	<a class="hb-event" href="prilezitost/<?php echo $opportunity->getId(); //todo: using rather WP routing somehow ?>">
		<div class="hb-event__imageWrapper">
			<img alt="" class="hb-event__image" data-src="<?php echo $opportunity->getImage()->getMediumSizePath(); ?>">
			<noscript><?php // for search engines ?>
				<img alt="" class="hb-event__image" src="<?php echo $opportunity->getImage()->getMediumSizePath(); ?>">
			</noscript>

			<div class="hb-event__labels eventLabels">
				<div class="eventLabels__item">
					<?php echo hb_opportunityCategoryToString($opportunity->getCategory()); ?>
				</div>
			</div>
		</div>

		<header class="hb-event__header">
			<h4 class="hb-event__heading">
				<?php echo $opportunity->getName(); ?>
			</h4>

			<div class="hb-event__meta">
				<time class="hb-event__date" datetime="{$event->dateStartForRobots}">
					<?php echo hb_dateSpan($opportunity->getStartDate(), $opportunity->getEndDate(), $dateFormat); ?>
				</time>

				<span class="hb-event__place" title="Místo konání">
					<?php echo $opportunity->getLocation()->getName(); ?>
				</span>
			</div>
		</header>

		<div class="hb-event__excerpt">
			<?php
				$text = (string) $opportunity->getIntroduction();
				$text = \strip_tags($text);
				$text = Strings::truncate($text, 200);
				$text = \nl2br($text);
				echo $text;
			?>
		</div>
	</a>
<?php } ?>

<main class="zapoj-se" role="main" id="obsah">
	<section>
		<h1>Aktuální příležitosti</h1>

		<div class="zapoj-se__filters filters hb-expandable" <?php if ($isAnySelected): ?> data-hb-expandable-expanded="1"<?php endif; ?>>
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

		<div class="zapoj-se__opportunities hb-eventList">
			<?php if ($hasBeenUnableToLoad): ?>
				<div class="hb-eventList__noResults noResults">
					Promiň, zrovna&nbsp;nám vypadl systém, kde&nbsp;máme uloženy všechny informace o&nbsp;příležitostech.
					Zkus&nbsp;to prosím za&nbsp;chvilku znovu.
				</div>

			<?php elseif (\count($opportunities) === 0): ?>
				<div class="hb-eventList__noResults noResults">
					Zrovna&nbsp;tu žádné příležitosti nejsou, zkus&nbsp;to později.
				</div>

			<?php else: ?>
				<div class="hb-eventList__grid">
					<?php $i = 1; foreach ($opportunities as $opportunity) {
						hb_opportunity($opportunity, $hb_dateFormat);
						if ($i === $numberOfOpportunitiesToDisplayOnLoad) break;
						$i++;
					} ?>
				</div>

				<?php if (\count($opportunities) > $numberOfOpportunitiesToDisplayOnLoad): ?>
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
								hb_opportunity($opportunity, $hb_dateFormat);
							} ?>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>


		<h1>Zapoj se do Brontosaura</h1>

		<div class="description">
			<div class="description-list">
				<a class="description-list-item optionBox button--secondary-wrapper" href="<?php echo \HnutiBrontosaurus\Theme\getLinkFor('organizovani'); ?>">
					<div class="description-list-item-text optionBox__text">
						<h2 class="optionBox__heading">
							Chci organizovat akce
						</h2>

						<p class="optionBox__description">
							Zapoj se do organizování akcí, naber zkušenosti
							na organizátorských kurzech nebo získej podporu v podobě
							mentoringu, propagace, financí, online nástrojů či výběru
							správné lokality.
						</p>
					</div>

					<div class="optionBox__image optionBox__image--organizovani">
						<div class="button button--secondary">Více info</div>
					</div>
				</a>

				<a class="description-list-item optionBox button--secondary-wrapper" href="<?php echo \HnutiBrontosaurus\Theme\getLinkFor('spoluprace'); ?>">
					<div class="description-list-item-text optionBox__text">
						<h2 class="optionBox__heading">
							Chci spolupracovat
						</h2>

						<p class="optionBox__description">
							Začni s námi spolupracovat na péči o přírodu,
							pořádání Ekostanů, činnosti dětských oddílů či organizaci soutěží.
							Zapoj se jako dobrovolník, lektor či patron budkování - dle tvých možností.
						</p>
					</div>

					<div class="optionBox__image optionBox__image--spoluprace">
						<div class="button button--secondary">Více info</div>
					</div>
				</a>

				<a class="description-list-item optionBox button--secondary-wrapper" href="<?php echo \HnutiBrontosaurus\Theme\getLinkFor('clenstvi'); ?>">
					<div class="description-list-item-text optionBox__text">
						<h2 class="optionBox__heading">
							Chci se stát členem
						</h2>

						<p class="optionBox__description">
							Členství v Hnutí Brontosaurus ti přinese spoustu skvělých výhod a zároveň podpoříš naši činnost a péči o přírodu a památky.
						</p>
					</div>

					<div class="optionBox__image optionBox__image--clenstvi">
						<div class="button button--secondary">Více info</div>
					</div>
				</a>

				<a class="description-list-item optionBox button--secondary-wrapper" href="https://mozek.brontosaurus.cz/index.php/cely-mozek/provoz-clanku-klubu-rc/zakladame-zc-rc-klub" rel="noopener" target="_blank">
					<div class="description-list-item-text optionBox__text">
						<h2 class="optionBox__heading">
							Chci založit článek, klub či oddíl
						</h2>

						<p class="optionBox__description">
							Našim základním článkům, klubům a dětským oddílům zajišťujeme finanční a marketingovou podporu, pojištění, slevy a mnoho dalších výhod.
						</p>
					</div>

					<div class="optionBox__image optionBox__image--zalozeni-clanku">
						<div class="button button--secondary">Více info</div>
					</div>
				</a>
			</div>
		</div>

		<div class="zapoj-se__map">
			<h2>Chceš se zapojit? Ozvi se nám!</h2>

			<?php $hb_latte->render(__DIR__ . '/../../UI/components/administrativeUnitsMap.latte', $params); ?>
		</div>

		<div class="hb-block-text">
			<h2>Kontakty na lokální koordinátory</h2>

			<ul class="hb-ta-c" style="list-style-type: none">
				<li><strong>Praha:</strong>
					Rostislav Konůpka,
					<a href="tel:+420722561150" rel="noopener noreferrer" target="_blank">722 561 150</a>,
					<a href="mailto:praha@brontosaurus.cz" rel="noopener noreferrer" target="_blank">praha@brontosaurus.cz</a>
				</li>

				<li><strong>Brno:</strong>
					Tereza Opravilová,
					<a href="tel:+420736720568" rel="noopener noreferrer" target="_blank">736 720 568</a>,
					<a href="mailto:akce-priroda@brontosaurus.cz" rel="noopener noreferrer" target="_blank">akce-priroda@brontosaurus.cz</a>
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
			Ještě by&nbsp;tě mohlo zajímat
		</h1>

		<div class="related-list">
			<a class="related-list-item related-list-item--events button--secondary-wrapper" href="<?php echo \HnutiBrontosaurus\Theme\getLinkFor('co-se-chysta'); ?>">
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

			<a class="related-list-item related-list-item--podpor-nas button--secondary-wrapper" href="<?php echo \HnutiBrontosaurus\Theme\getLinkFor('podpor-nas'); ?>">
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
						E-shop s&nbsp;dárky přírodě
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
