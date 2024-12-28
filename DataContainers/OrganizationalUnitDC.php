<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\AdministrationUnit;
use HnutiBrontosaurus\Theme\PropertyHandler;


/**
 * @property-read string $name
 * @property-read string $address
 * @property-read string $website
 * @property-read string $emailAddress
 */
final class OrganizationalUnitDC
{
	use PropertyHandler;

	private function __construct(
		private string $name,
		private string $address,
		private ?string $website,
		private ?string $emailAddress,
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
