<?php declare(strict_types = 1);

use Grifart\GeocodingClient\MapyCz\NoResultException;
use HnutiBrontosaurus\BisApiClient\BisApiClientRuntimeException;
use HnutiBrontosaurus\Theme\UI\AboutStructure\AboutStructureController;
use HnutiBrontosaurus\Theme\UI\DataContainers\Structure\OrganizationalUnitDC;


$configuration = hb_getConfiguration();
$bisApiClient = hb_getBisApiClient($configuration);
$latte = hb_getLatte();
$geocodingClient = hb_getGeocodingClient();

$hasBeenUnableToLoad = false;

try {
	foreach ($bisApiClient->getOrganizationalUnits() as $organizationalUnit) {
		try {
			$location = $geocodingClient->getCoordinatesFor(
				$organizationalUnit->getStreet(),
				$organizationalUnit->getCity(),
				$organizationalUnit->getPostCode()
			);
			$organizationalUnits[] = OrganizationalUnitDC::fromDTO($organizationalUnit, $location);

		} catch (NoResultException) {
			continue; // in case of non-existing address just silently continue and ignore this unit
		}
	}

} catch (BisApiClientRuntimeException) {
	$hasBeenUnableToLoad = true;
}

$viewAssetsPath = get_template_directory_uri() . '/UI/AboutStructure/assets';

$params = [
	'viewAssetsPath' => $viewAssetsPath,
	'organizationalUnitsInJson' => AboutStructureController::getOrganizationalUnitsInJson($organizationalUnits),
	'hasBeenUnableToLoad' => $hasBeenUnableToLoad,
];

?>

<main class="pridej-se" role="main" id="obsah">
	<section>
		<h1>Chceš se zapojit? Ozvi se nám!</h1>

		<?php $latte->render(__DIR__ . '/../../UI/AboutStructure/unitMap.latte', $params); ?>

		<div class="hb-block-text">
			<h2>Kontakty na lokální koordinátory</h2>

			<ul class="hb--centered" style="list-style-type: none">
				<li><strong>Praha:</strong>
					Daniela Syrovátková,
					<a href="tel:+420777907125" rel="noopener noreferrer" target="_blank">777 907 125</a>,
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

		<div class="description">
			<div class="description-list">
				<a class="description-list-item optionBox button--secondary-wrapper" href="<?php echo \HnutiBrontosaurus\Theme\getLinkFor('chci-se-zapojit'); ?>">
					<div class="description-list-item-text optionBox__text">
						<h2 class="optionBox__heading">
							Chci se zapojit
						</h2>

						<p class="optionBox__description">
							Zapoj se do organizování našich akcí dle svých možností třeba jako kuchař, zdravotník či fotograf.
							Spolupracovat s námi můžeš také jako lektor, grafik nebo třeba tvůrce webů.
						</p>
					</div>

					<div class="optionBox__image optionBox__image--chci-se-zapojit">
						<div class="button button--secondary">Více informací</div>
					</div>
				</a>

				<a class="description-list-item optionBox button--secondary-wrapper" href="https://mozek.brontosaurus.cz/index.php/cely-mozek/organizovani-akci/jak-se-stat-organizatorem" rel="noopener" target="_blank">
					<div class="description-list-item-text optionBox__text">
						<h2 class="optionBox__heading">
							Chci organizovat akci
						</h2>

						<p class="optionBox__description">
							Nauč se pořádat vlastní dobrovolnické akce na našich organizátorských kurzech a získej podporu
							v podobě mentoringu, propagace, financí, online nástrojů či výběru správné lokality.
						</p>
					</div>

					<div class="optionBox__image optionBox__image--chci-organizovat-akci">
						<div class="button button--secondary">Více informací</div>
					</div>
				</a>

				<a class="description-list-item optionBox button--secondary-wrapper" href="<?php echo \HnutiBrontosaurus\Theme\getLinkFor('chci-se-stat-clenem'); ?>">
					<div class="description-list-item-text optionBox__text">
						<h2 class="optionBox__heading">
							Chci se stát členem
						</h2>

						<p class="optionBox__description">
							Členství v Hnutí Brontosaurus ti přinese spoustu skvělých výhod a zároveň podpoříš naši činnost a péči o přírodu a památky.
						</p>
					</div>

					<div class="optionBox__image optionBox__image--chci-se-stat-clenem">
						<div class="button button--secondary">Více informací</div>
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

					<div class="optionBox__image optionBox__image--chci-zalozit-clanek">
						<div class="button button--secondary">Více informací</div>
					</div>
				</a>
			</div>
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

<script src="<?php echo $viewAssetsPath; ?>/dist/js/index.js" type="text/javascript"></script><?php // maybe rather by using add_action('wp_enqueue_scripts') ?>
