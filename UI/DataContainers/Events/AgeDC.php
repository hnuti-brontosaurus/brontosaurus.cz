<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\BisApiClient\Response\Event\Event;
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


	/** @var bool */
	private $isListed = false;

	/** @var bool */
	private $isInterval = false;

	/** @var bool */
	private $isFromListed = false;

	/** @var int|null */
	private $from;

	/** @var bool */
	private $isUntilListed = false;

	/** @var int|null */
	private $until;


	/**
	 * @param int|null $ageFrom
	 * @param int|null $ageUntil
	 */
	private function __construct($ageFrom = null, $ageUntil = null)
	{
		$this->isListed = true;

		if (self::validateAgeFromConstraints($ageFrom) && self::validateAgeUntilConstraints($ageUntil)) {
			$this->isInterval = true;
			$this->from = $ageFrom;
			$this->until = $ageUntil;

		} elseif (self::validateAgeFromConstraints($ageFrom)) {
			$this->isFromListed = true;
			$this->from = $ageFrom;

		} elseif (self::validateAgeUntilConstraints($ageUntil)) {
			$this->isUntilListed = true;
			$this->until = $ageUntil;

		} else {
			$this->isListed = false;

		}
	}

	public static function fromDTO(Event $event)
	{
		return new self(
			$event->getAgeFrom(),
			$event->getAgeUntil()
		);
	}


	/**
	 * Filter our stuff like "from 0 years".
	 * One valid use-case for that would be when organizers would like to indicate that an event is even baby-friendly, but nobody used it like this yet, only "0-60" values are present which is probably pointless information.
	 * @param int|null $ageFrom
	 * @return bool
	 */
	private static function validateAgeFromConstraints($ageFrom)
	{
		return $ageFrom !== null && $ageFrom !== 0;
	}

	/**
	 * Filter out stuff like "until 99 years".
	 * @param int|null $ageUntil
	 * @return bool
	 */
	private static function validateAgeUntilConstraints($ageUntil)
	{
		return $ageUntil !== null && $ageUntil < 90;
	}

}
