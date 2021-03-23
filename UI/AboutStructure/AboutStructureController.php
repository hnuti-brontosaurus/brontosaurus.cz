<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\AboutStructure;

use Grifart\GeocodingClient\MapyCz\NoResultException;
use HnutiBrontosaurus\BisApiClient\BisApiClientRuntimeException;
use HnutiBrontosaurus\BisApiClient\Client;
use HnutiBrontosaurus\Theme\UI\Base\Base;
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

		$params = [
			'viewAssetsPath' => get_template_directory_uri() . '/UI/AboutStructure/assets',
			'organizationalUnitsInJson' => self::getOrganizationalUnitsInJson($organizationalUnits),
			'hasBeenUnableToLoad' => $hasBeenUnableToLoad,
			'contactsPageLink' => $this->base->getLinkFor('kontakty'),
		];

		$this->latte->render(
			__DIR__ . '/AboutStructureController.latte',
			\array_merge($this->base->getLayoutVariables('aboutstructure'), $params),
		);
	}


	/**
	 * Creates structure for Google Maps API.
	 * @param OrganizationalUnitDC[] $organizationalUnits
	 */
	private static function getOrganizationalUnitsInJson(array $organizationalUnits): string
	{
		$organizationalUnits = \array_map(
			static fn(OrganizationalUnitDC $organizationalUnit): array => [
				'name' => $organizationalUnit->name,
				'lat' => $organizationalUnit->coordinates->latitude,
				'lng' => $organizationalUnit->coordinates->longitude,
				'address' => [
					'street' => $organizationalUnit->address->street,
					'postCode' => $organizationalUnit->address->postCode,
					'city' => $organizationalUnit->address->city,
				],
				'chairman' => $organizationalUnit->chairman,
				'website' => $organizationalUnit->website,
				'email' => $organizationalUnit->emailAddress,
				'isOfTypeClub' => $organizationalUnit->isOfTypeClub,
				'isOfTypeBase' => $organizationalUnit->isOfTypeBase,
				'isOfTypeRegional' => $organizationalUnit->isOfTypeRegional,
				'isOfTypeOffice' => $organizationalUnit->isOfTypeOffice,
				'isOfTypeChildren' => $organizationalUnit->isOfTypeChildren,
			],
			$organizationalUnits
		);

		return \json_encode($organizationalUnits);
	}

}
