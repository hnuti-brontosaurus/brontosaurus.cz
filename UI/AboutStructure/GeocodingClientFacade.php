<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\AboutStructure;

use Grifart\GeocodingClient\GeocodingService;
use Grifart\GeocodingClient\Location;
use Grifart\GeocodingClient\MapyCz\NoResultException;


final class GeocodingClientFacade
{

	public function __construct(
		private GeocodingService $geocodingService,
	) {}

	/**
	 * @return Location
	 * @throws NoResultException
	 */
	public function getCoordinatesFor(string $street, string $city, string $postCode): Location
	{
		$results = $this->geocodingService->geocodeAddress(\sprintf('%s, %s, %s', $street, $city, $postCode));

		$result = \reset($results); // we want only first result
		\assert($result !== false);

		return $result;
	}

}
