<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\BaseUnitsAndClubsList;

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Response\AdministrationUnit\AdministrationUnit;
use HnutiBrontosaurus\BisClient\Response\OrganizationalUnit\OrganizationalUnit;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class BaseUnitsAndClubsListController implements Controller
{
	public const PAGE_SLUG = 'zakladni-clanky-a-kluby';

	public function __construct(
		private BisClient $bisApiClient,
		private Base $base,
		private Engine $latte,
	) {}

	public function render(): void
	{
		$hasBeenUnableToLoad = false;

		try {
			// get all organizational units
			$units = $this->bisApiClient->getAdministrationUnits();

			// filter only base units and clubs
			$units = \array_filter(
				$units,
				static fn(AdministrationUnit $unit): bool
					=> $unit->isBaseUnit() || $unit->isClub()
			);

			// transfer DTOs to DCs
			$units = \array_map(
				static fn(AdministrationUnit $unit): OrganizationalUnitDC
					=> OrganizationalUnitDC::fromDTO($unit),
				$units,
			);

		} catch (ConnectionToBisFailed) {
			$hasBeenUnableToLoad = true;
		}

		$params = [
			'hasBeenUnableToLoad' => $hasBeenUnableToLoad,
			'units' => $units,
		];

		$this->latte->render(
			__DIR__ . '/BaseUnitsController.latte',
			\array_merge($this->base->getLayoutVariables('baseunits'), $params),
		);
	}

}
