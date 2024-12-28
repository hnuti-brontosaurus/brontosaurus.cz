<?php

namespace HnutiBrontosaurus\Theme\CoordinatesResolver;

use Grifart\GeocodingClient\GeocodingFailed;
use Grifart\GeocodingClient\GeocodingProvider;
use Grifart\GeocodingClient\Providers\MapyCz\ConnectionToMapyCzApiFailed;
use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\AdministrationUnit;
use HnutiBrontosaurus\Theme\CannotResolveCoordinates;
use function assert;
use function reset;


final class CoordinatesResolver
{

	public function __construct(
		private GeocodingProvider $geocodingProvider,
	) {}


	/**
	 * @throws CannotResolveCoordinates
	 */
	public function resolve(AdministrationUnit $administrationUnit): Coordinates
	{
		// use coordinates included in response from BIS if possible
		if (($coordinates = $administrationUnit->getCoordinates()) !== null) {
			return Coordinates::from($coordinates->getLatitude(), $coordinates->getLongitude());
		}

		// otherwise try geocoding
		try {
			$results = $this->geocodingProvider->geocode($administrationUnit->getAddress());
			$location = reset($results); // use first result
			assert($location !== false);

			return Coordinates::from($location->getLatitude(), $location->getLongitude());

		} catch (GeocodingFailed|ConnectionToMapyCzApiFailed $e) {
			throw new CannotResolveCoordinates(previous: $e);
		}
	}

}
