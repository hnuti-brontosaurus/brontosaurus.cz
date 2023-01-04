<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Response\Event\Event;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


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
		$ageFrom = $event->getAgeFrom();
		$ageFromValid = self::ageFromInValidConstraints($ageFrom);
		$ageUntil = $event->getAgeUntil();
		$ageUntilValid = self::ageUntilInValidConstraints($ageUntil);

		return new self(
			isListed: $ageFromValid || $ageUntilValid,
			isInterval: $ageFromValid && $ageUntilValid,
			isFromListed: $ageFromValid && ! $ageUntilValid,
			from: $ageFrom,
			isUntilListed: ! $ageFromValid && $ageUntilValid,
			until: $ageUntil,
		);
	}


	/**
	 * Filter our stuff like "from 0 years".
	 * One valid use-case for that would be when organizers would like to indicate that an event is even baby-friendly, but nobody used it like this yet, only "0-60" values are present which is probably pointless information.
	 */
	private static function ageFromInValidConstraints(?int $ageFrom): bool
	{
		return $ageFrom !== null && $ageFrom !== 0;
	}

	/**
	 * Filter out stuff like "until 99 years".
	 */
	private static function ageUntilInValidConstraints(?int $ageUntil): bool
	{
		return $ageUntil !== null && $ageUntil < 90;
	}

}
