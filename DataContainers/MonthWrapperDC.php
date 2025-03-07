<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\PropertyHandler;


/**
 * @property-read int $monthNumber
 * @property-read EventCollectionDC|null $events
 */
final class MonthWrapperDC
{
	use PropertyHandler;


	private int $monthNumber;
	private ?EventCollectionDC $events = null;


	public function __construct(int $monthNumber)
	{
		$this->monthNumber = $monthNumber;
	}


	public function addEvent(Event $event, string $dateFormatHuman, string $dateFormatRobot): void
	{
		if ($this->events === null) {
			$this->events = new EventCollectionDC(NULL, $dateFormatHuman, $dateFormatRobot);
		}

		$this->events->add($event);
	}

}
