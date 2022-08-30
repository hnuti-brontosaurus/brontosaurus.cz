<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers;

use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Event;
use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read int $monthNumber
 * @property-read EventCollectionDC $events
 */
final class MonthWrapperDC
{

	use PropertyHandler;


	/** @var int */
	private $monthNumber;

	/** @var EventCollectionDC */
	private $events;


	/**
	 * @param int $monthNumber
	 */
	public function __construct($monthNumber)
	{
		$this->monthNumber = $monthNumber;
	}


	/**
	 * @param Event $event
	 * @param string $dateFormatHuman
	 * @param string $dateFormatRobot
	 * @return void
	 */
	public function addEvent(Event $event, $dateFormatHuman, $dateFormatRobot)
	{
		if ($this->events === NULL) {
			$this->events = new EventCollectionDC(NULL, $dateFormatHuman, $dateFormatRobot);
		}

		$this->events->add($event);
	}

}
