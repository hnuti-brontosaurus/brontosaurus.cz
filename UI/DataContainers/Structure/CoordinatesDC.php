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


	private function __construct(
		private float $latitude,
		private float $longitude,
	) {}


	public static function fromDTO(Coordinates $coordinates): self
	{
		return new self(
			$coordinates->getLatitude(),
			$coordinates->getLongitude(),
		);
	}

}
