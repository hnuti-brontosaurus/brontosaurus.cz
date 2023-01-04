<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\BaseUnitsAndClubsList;

use HnutiBrontosaurus\BisClient\Response\AdministrationUnit\AdministrationUnit;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


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
