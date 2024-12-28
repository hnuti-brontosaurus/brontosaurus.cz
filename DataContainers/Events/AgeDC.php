<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\Theme\PropertyHandler;


/**
 * @property-read bool $isListed
 * @property-read bool $isInterval
 * @property-read bool $isFromListed
 * @property-read int|null $from
 * @property-read bool $isUntilListed
 * @property-read int|null $until
 */
final class AgeDC
{
	use PropertyHandler;

	private function __construct(
		private bool $isListed,
		private bool $isInterval,
		private bool $isFromListed,
		private ?int $from,
		private bool $isUntilListed,
		private ?int $until,
	) {}

	public static function fromDTO(Event $event): self
	{
		$ageFrom = $event->getPropagation()->getMinimumAge();
		$ageFromListed = $ageFrom !== null;
		$ageUntil = $event->getPropagation()->getMaximumAge();
		$ageUntilListed = $ageUntil !== null;

		return new self(
			isListed: $ageFromListed || $ageUntilListed,
			isInterval: $ageFromListed && $ageUntilListed,
			isFromListed: $ageFromListed && ! $ageUntilListed,
			from: $ageFrom,
			isUntilListed: ! $ageFromListed && $ageUntilListed,
			until: $ageUntil,
		);
	}

}
