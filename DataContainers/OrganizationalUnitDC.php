<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\AdministrationUnit;


final class OrganizationalUnitDC
{

	private function __construct(
		public readonly string $name,
		public readonly string $address,
		public readonly ?string $website,
		public readonly ?string $emailAddress,
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
