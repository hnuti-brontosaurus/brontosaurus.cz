<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Response\ContactPerson;


final class ContactDC
{

	private function __construct(
		public readonly bool $isPersonListed,
		public readonly ?string $person,
		public readonly string $email,
		public readonly bool $isPhoneListed,
		public readonly ?string $phone,
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
