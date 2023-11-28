<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\AboutStructure;

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\Theme\CannotResolveCoordinates;
use HnutiBrontosaurus\Theme\CoordinatesResolver\CoordinatesResolver;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\BaseUnitsAndClubsList\BaseUnitsAndClubsListController;
use HnutiBrontosaurus\Theme\UI\Controller;
use HnutiBrontosaurus\Theme\UI\DataContainers\Structure\AdministrationUnitDC;
use Latte\Engine;
use Tracy\Debugger;
use Tracy\ILogger;


/*
 * Controller is left with old name (about structure) as we need to save time.
 */
final class AboutStructureController implements Controller
{
	public const PAGE_SLUG = 'hnuti-brontosaurus';

	public function __construct(
		private BisClient $bisApiClient,
		private Base $base,
		private Engine $latte,
		private CoordinatesResolver $coordinatesResolver,
	) {}


	public function render(): void
	{
		$administrationUnits = [];
		$hasBeenUnableToLoad = false;

		try {
			foreach ($this->bisApiClient->getAdministrationUnits() as $organizationalUnit) {
				try {
					$coordinates = $this->coordinatesResolver->resolve($organizationalUnit);
					$administrationUnits[] = AdministrationUnitDC::fromDTO($organizationalUnit, $coordinates);

				} catch (CannotResolveCoordinates $e) {
					Debugger::log($e, ILogger::WARNING);
					continue; // if can not resolve (e.g. non-existing address) just ignore this unit and continue
				}
			}

		} catch (ConnectionToBisFailed) {
			$hasBeenUnableToLoad = true;
		}

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-aboutStructure-map', $theme->get_template_directory_uri() . '/frontend/dist/js/administrativeUnitsMap.js', [], $themeVersion);
		});

		$params = [
			'administrationUnitsInJson' => \json_encode($administrationUnits),
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
