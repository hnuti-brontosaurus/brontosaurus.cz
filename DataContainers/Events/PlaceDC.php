<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Response\Location;
use HnutiBrontosaurus\Theme\PropertyHandler;


/**
 * @property-read string $name
 * @property-read bool $areCoordinatesListed
 * @property-read string|null $coordinates
 */
final class PlaceDC
{
	use PropertyHandler;

	private function __construct(
		private string $name,
		private bool $areCoordinatesListed,
		private ?string $coordinates,
	) {}


	public static function fromDTO(Location $place): self
	{
		$coordinates = $place->getCoordinates();
		return new self(
			hb_handleNonBreakingSpaces($place->getName()),
			$coordinates !== null,
			$coordinates !== null
				? $coordinates->getLatitude() . ' ' . $coordinates->getLongitude() // e.g. 49.132456 16.123456
				: null,
		);
	}

}
