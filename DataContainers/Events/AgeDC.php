<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Event\Response\Event;


final class AgeDC
{

	private function __construct(
		public readonly bool $isListed,
		public readonly bool $isInterval,
		public readonly bool $isFromListed,
		public readonly ?int $from,
		public readonly bool $isUntilListed,
		public readonly ?int $until,
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
