<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventCollectionDC;


final class MonthWrapperDC
{

	public /*get*/ ?EventCollectionDC $events = null;


	public function __construct(
		public readonly int $monthNumber,
	) {}


	public function addEvent(Event $event, string $dateFormatHuman, string $dateFormatRobot): void
	{
		if ($this->events === null) {
			$this->events = new EventCollectionDC(NULL, $dateFormatHuman, $dateFormatRobot);
		}

		$this->events->add($event);
	}

}
