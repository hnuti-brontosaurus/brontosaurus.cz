<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Response\Location;


final readonly class PlaceDC
{

	private function __construct(
		public string $name,
		public bool $areCoordinatesListed,
		public ?string $coordinates,
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
