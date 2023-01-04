<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Structure;

use HnutiBrontosaurus\Theme\CoordinatesResolver\Coordinates;
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
	public static function fromDTO(Coordinates $coordinates)
	{
		return new self(
			$coordinates->getLatitude(),
			$coordinates->getLongitude()
		);
	}

}
