<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\BisApiClient\Response\Event\Organizer;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $person
 * @property-read string $email
 * @property-read string $phone
 */
final class ContactDC
{
	use PropertyHandler;


	/** @var string */
	private $person;

	/** @var string */
	private $email;

	/** @var string */
	private $phone;


	/**
	 * @param string $person
	 * @param string $email
	 * @param string $phone
	 */
	private function __construct($person, $email, $phone)
	{
		$this->person = $person;
		$this->email = $email;
		$this->phone = $phone;
	}

	/**
	 * @return self
	 */
	public static function fromDTO(Organizer $organizer)
	{
		return new self(
			$organizer->getContactPersonName(),
			$organizer->getContactEmail(),
			$organizer->getContactPhone()
		);
	}

}
