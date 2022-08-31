<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\AboutStructure;

use Grifart\GeocodingClient\MapyCz\NoResultException;
use HnutiBrontosaurus\LegacyBisApiClient\BisApiClientRuntimeException;
use HnutiBrontosaurus\LegacyBisApiClient\Client;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\BaseUnitsAndClubsList\BaseUnitsAndClubsListController;
use HnutiBrontosaurus\Theme\UI\Controller;
use HnutiBrontosaurus\Theme\UI\DataContainers\Structure\OrganizationalUnitDC;
use Latte\Engine;


/*
 * Controller is left with old name (about structure) as we need to save time.
 */
final class AboutStructureController implements Controller
{
	public const PAGE_SLUG = 'hnuti-brontosaurus';

	public function __construct(
		private string $dateFormatHuman,
		private string $dateFormatRobot,
		private Client $bisApiClient,
		private Base $base,
		private Engine $latte,
		private GeocodingClientFacade $geocodingClientFacade,
	) {}


	public function render(): void
	{
		$organizationalUnits = [];
		$hasBeenUnableToLoad = false;

		try {
			foreach ($this->bisApiClient->getOrganizationalUnits() as $organizationalUnit) {
				try {
					$location = $this->geocodingClientFacade->getCoordinatesFor(
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

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-aboutStructure-map', $theme->get_template_directory_uri() . '/frontend/dist/js/administrativeUnitsMap.js', [], $themeVersion);
		});

		$params = [
			'organizationalUnitsInJson' => \json_encode($organizationalUnits),
			'hasBeenUnableToLoad' => $hasBeenUnableToLoad,
			'baseUnitsAndClubsLink' => $this->base->getLinkFor(BaseUnitsAndClubsListController::PAGE_SLUG),
			'contactsPageLink' => $this->base->getLinkFor('kontakty'),
		];

		$this->latte->render(
			__DIR__ . '/AboutStructureController.latte',
			\array_merge($this->base->getLayoutVariables('aboutstructure'), $params),
		);
	}

}
