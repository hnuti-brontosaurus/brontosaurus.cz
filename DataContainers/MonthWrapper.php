<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventCollection;


final class MonthWrapper
{

	public private(set) ?EventCollection $events = null;


	public function __construct(
		public readonly int $monthNumber,
	) {}


	public function addEvent(Event $event, string $dateFormatHuman, string $dateFormatRobot): void
	{
		if ($this->events === null) {
			$this->events = new EventCollection(NULL, $dateFormatHuman, $dateFormatRobot);
		}

		$this->events->add($event);
	}

}
