<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Response\ContactPerson;


final readonly class Contact
{

	private function __construct(
		public bool $isPersonListed,
		public ?string $person,
		public string $email,
		public bool $isPhoneListed,
		public ?string $phone,
	) {}


	public static function fromDTO(ContactPerson $contactPerson): self
	{
		return new self(
			$contactPerson->name !== null,
			$contactPerson->name,
			$contactPerson->emailAddress,
			$contactPerson->phoneNumber !== null,
			$contactPerson->phoneNumber,
		);
	}

}
