<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Structure;

use Grifart\GeocodingClient\Location;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read float $latitude
 * @property-read float $longitude
 */
final class CoordinatesDC
{

	use PropertyHandler;


	/** @var float */
	private $latitude;

	/** @var float */
	private $longitude;


	/**
	 * @param float $latitude
	 * @param float $longitude
	 */
	private function __construct($latitude, $longitude)
	{
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}


	/**
	 * @return CoordinatesDC
	 */
	public static function fromDTO(Location $location)
	{
		return new self(
			$location->getLatitude(),
			$location->getLongitude()
		);
	}

}
