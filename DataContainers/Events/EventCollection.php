<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use ArrayIterator;
use HnutiBrontosaurus\BisClient\Event\Response\Event as EventFromClient;
use IteratorAggregate;
use function array_map;
use function count;


final class EventCollection implements IteratorAggregate
{

	public private(set) bool $hasAny = false;
	public private(set) bool $hasBeenUnableToLoad = false; // this is used when BIS is not available
	public private(set) int $count = 0;
	/** @var Event[] */
	public private(set) array $events = [];

	private string $dateFormatHuman;
	private string $dateFormatRobot;


	/**
	 * @param EventFromClient[] $events
	 */
	public function __construct(?array $events, string $dateFormatHuman, string $dateFormatRobot)
	{
		$this->dateFormatHuman = $dateFormatHuman;
		$this->dateFormatRobot = $dateFormatRobot;

		if ($events !== null && count($events) > 0) {
			$this->events = array_map(function (EventFromClient $event) {
				return new Event($event, $this->dateFormatHuman, $this->dateFormatRobot);
			}, $events);
			$this->hasAny = true;
			$this->count = count($events);
		}
	}


	public function add(EventFromClient $event): void
	{
		$this->events[] = new Event($event, $this->dateFormatHuman, $this->dateFormatRobot);
		$this->hasAny = true;
		$this->count++;
	}

	public function setUnableToLoad(): void
	{
		$this->hasBeenUnableToLoad = true;
	}


	public static function unableToLoad(string $dateFormatHuman, string $dateFormatRobot): static
	{
		$instance = new self([], $dateFormatHuman, $dateFormatRobot);
		$instance->setUnableToLoad();
		return $instance;
	}


	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->events);
	}
}
