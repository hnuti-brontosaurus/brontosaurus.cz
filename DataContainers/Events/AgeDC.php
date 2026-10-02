<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Event\Response\Event;


final readonly class AgeDC
{

	private function __construct(
		public bool $isListed,
		public bool $isInterval,
		public bool $isFromListed,
		public ?int $from,
		public bool $isUntilListed,
		public ?int $until,
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
