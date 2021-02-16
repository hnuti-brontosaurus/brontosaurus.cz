<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $title
 * @property-read string $icon
 */
final class EventTypeDC
{

	use PropertyHandler;


	/** @var string */
	private $title;

	/** @var string */
	private $icon;


	/**
	 * @param string $title
	 * @param string $icon
	 */
	public function __construct($title, $icon)
	{
		$this->title = $title;
		$this->icon = $icon;
	}

}
