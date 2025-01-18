<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Response\ContactPerson;
use HnutiBrontosaurus\Theme\PropertyHandler;


/**
 * @property-read bool $isPersonListed
 * @property-read string|null $person
 * @property-read string $email
 * @property-read bool $isPhoneListed
 * @property-read string|null $phone
 */
final class ContactDC
{
	use PropertyHandler;

	private function __construct(
		private bool $isPersonListed,
		private ?string $person,
		private string $email,
		private bool $isPhoneListed,
		private ?string $phone,
	) {}


	public static function fromDTO(ContactPerson $contactPerson): self
	{
		return new self(
			$contactPerson->getName() !== null,
			$contactPerson->getName(),
			$contactPerson->getEmailAddress(),
			$contactPerson->getPhoneNumber() !== null,
			$contactPerson->getPhoneNumber(),
		);
	}

}
