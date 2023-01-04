<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Event;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read bool $hasAny
 * @property-read bool $hasBeenUnableToLoad
 * @property-read int $count
 * @property-read EventDC[] $events
 */
final class EventCollectionDC implements \IteratorAggregate
{
	use PropertyHandler;

	private bool $hasAny = false;
	private bool $hasBeenUnableToLoad = false; // this is used when BIS is not available
	private int $count = 0;
	/** @var EventDC[] */
	private array $events = [];


	// really private :-)

	private string $dateFormatHuman;
	private string $dateFormatRobot;


	/**
	 * @param Event[] $events
	 */
	public function __construct(?array $events, string $dateFormatHuman, string $dateFormatRobot)
	{
		$this->dateFormatHuman = $dateFormatHuman;
		$this->dateFormatRobot = $dateFormatRobot;

		if ($events !== null && \count($events) > 0) {
			$this->events = \array_map(function (Event $event) {
				return EventDC::fromDTO($event, $this->dateFormatHuman, $this->dateFormatRobot);
			}, $events);
			$this->hasAny = true;
			$this->count = \count($events);
		}
	}


	public function add(Event $event): void
	{
		$this->events[] = EventDC::fromDTO($event, $this->dateFormatHuman, $this->dateFormatRobot);
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


	public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this->events);
	}
}
