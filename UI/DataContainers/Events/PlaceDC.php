<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\BisApiClient\Response\Event\Place;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use HnutiBrontosaurus\Theme\UI\Utils;


/**
 * @property-read string $name
 * @property-read bool $areCoordinatesListed
 * @property-read string|null $coordinates
 */
final class PlaceDC
{
	use PropertyHandler;


	/** @var string */
	private $name;

	/** @var bool */
	private $areCoordinatesListed = false;

	/** @var string|null */
	private $coordinates;


	/**
	 * @param string $name
	 * @param string|null $coordinates
	 */
	private function __construct($name, $coordinates = null)
	{
		$this->name = Utils::handleNonBreakingSpaces($name);

		if ($coordinates !== null) {
			$this->areCoordinatesListed = true;
			$this->coordinates = $coordinates;
		}
	}

	/**
	 * @param Place $place
	 * @return self
	 */
	public static function fromDTO(Place $place)
	{
		return new self(
			$place->getName(),
			$place->areCoordinatesListed() ? $place->getCoordinates() : null
		);
	}

}
