<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\BaseUnitsAndClubsList;

use HnutiBrontosaurus\BisApiClient\BisApiClientRuntimeException;
use HnutiBrontosaurus\BisApiClient\Client;
use HnutiBrontosaurus\BisApiClient\Request\OrganizationalUnitParameters;
use HnutiBrontosaurus\BisApiClient\Response\OrganizationalUnit\OrganizationalUnit;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class BaseUnitsAndClubsListController implements Controller
{
	public const PAGE_SLUG = 'zakladni-clanky-a-kluby';

	public function __construct(
		private Client $bisApiClient,
		private Base $base,
		private Engine $latte,
	) {}

	public function render(): void
	{
		$params = new OrganizationalUnitParameters();

		$hasBeenUnableToLoad = false;

		try {
			// get all organizational units
			$units = $this->bisApiClient->getOrganizationalUnits($params);

			// filter only base units and clubs
			$units = \array_filter(
				$units,
				static fn(OrganizationalUnit $unit): bool
					=> $unit->isBaseUnit() || $unit->isClub()
			);

			// transfer DTOs to DCs
			$units = \array_map(
				static fn(OrganizationalUnit $unit): OrganizationalUnitDC
					=> OrganizationalUnitDC::fromDTO($unit),
				$units,
			);

		} catch (BisApiClientRuntimeException) {
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
