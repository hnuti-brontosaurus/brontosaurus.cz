<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\AdministrationUnit;


final readonly class OrganizationalUnit
{

	private function __construct(
		public string $name,
		public string $address,
		public ?string $website,
		public ?string $emailAddress,
	) {}


	public static function fromDTO(AdministrationUnit $organizationalUnit): self
	{
		return new self(
			$organizationalUnit->getName(),
			$organizationalUnit->getAddress(),
			$organizationalUnit->getWebsite(),
			$organizationalUnit->getEmail(),
		);
	}

}
