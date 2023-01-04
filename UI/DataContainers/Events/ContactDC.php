<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Response\ContactPerson;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $person
 * @property-read string $email
 * @property-read string $phone
 */
final class ContactDC
{
	use PropertyHandler;

	private function __construct(
		private string $person,
		private string $email,
		private string $phone,
	) {}


	public static function fromDTO(ContactPerson $contactPerson): self
	{
		return new self(
			$contactPerson->getName(),
			$contactPerson->getEmailAddress(),
			$contactPerson->getPhoneNumber(),
		);
	}

}
