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

<main class="zapoj-se" role="main" id="obsah">
	<section>
		<h1>Aktuální příležitosti</h1>
	</section>

	<section>
		<h1>Chceš se zapojit? Ozvi se nám!</h1>

		<?php $latte->render(__DIR__ . '/../../UI/AboutStructure/unitMap.latte', $params); ?>
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
