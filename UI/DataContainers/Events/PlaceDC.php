<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Place;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


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


	public static function fromDTO(Place $place): self
	{
		return new self(
			$place->getName(),
			$place->areCoordinatesListed(),
			$place->areCoordinatesListed() ? $place->getCoordinates() : null
		);
	}

}
