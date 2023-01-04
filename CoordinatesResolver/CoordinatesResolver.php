<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\CoordinatesResolver;

use Grifart\GeocodingClient\GeocodingService;
use Grifart\GeocodingClient\Location;
use Grifart\GeocodingClient\MapyCz\NoResultException;
use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\AdministrationUnit;
use HnutiBrontosaurus\Theme\CannotResolveCoordinates;


final class CoordinatesResolver
{

	public function __construct(
		private GeocodingService $geocodingService,
	) {}


	/**
	 * @throws CannotResolveCoordinates
	 */
	public function resolve(AdministrationUnit $organizationalUnit): Coordinates
	{
		// use coordinates included in response from BIS if possible
		if (($coordinates = $organizationalUnit->getCoordinates()) !== null) {
			return Coordinates::from($coordinates->getLatitude(), $coordinates->getLongitude());
		}

		// otherwise try geocoding
		try {
			$location = $this->resolveFromGeocoding($organizationalUnit);
			return Coordinates::from($location->getLatitude(), $location->getLongitude());

		} catch (NoResultException) {
			throw new CannotResolveCoordinates();
		}
	}


	/**
	 * @throws NoResultException
	 */
	private function resolveFromGeocoding(AdministrationUnit $administrationUnit): Location
	{
		$results = $this->geocodingService->geocodeAddress($administrationUnit->getAddress());

		$result = \reset($results); // we want only first result
		\assert($result !== false);

		return $result;
	}

}
