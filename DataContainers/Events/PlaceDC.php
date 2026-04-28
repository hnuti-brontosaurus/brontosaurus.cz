<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Response\Location;


final class PlaceDC
{

	private function __construct(
		public readonly string $name,
		public readonly bool $areCoordinatesListed,
		public readonly ?string $coordinates,
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
