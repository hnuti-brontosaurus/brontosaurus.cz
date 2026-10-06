<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Event\Response\Event;


final readonly class Age
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
		$ageFrom = $event->propagation->minimumAge;
		$ageFromListed = $ageFrom !== null;
		$ageUntil = $event->propagation->maximumAge;
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
