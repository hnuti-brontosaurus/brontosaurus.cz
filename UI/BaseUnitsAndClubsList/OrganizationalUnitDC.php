<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\BaseUnitsAndClubsList;

use HnutiBrontosaurus\LegacyBisApiClient\Response\OrganizationalUnit\OrganizationalUnit;
use HnutiBrontosaurus\Theme\UI\DataContainers\Structure\AddressDC;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $name
 * @property-read AddressDC $address
 * @property-read string $website
 * @property-read string $emailAddress
 */
final class OrganizationalUnitDC
{
	use PropertyHandler;

	private function __construct(
		private string $name,
		private AddressDC $address,
		private ?string $website,
		private ?string $emailAddress,
	) {}


	public static function fromDTO(OrganizationalUnit $organizationalUnit): self
	{
		return new self(
			$organizationalUnit->getName(),
			AddressDC::from(
				$organizationalUnit->getStreet(),
				$organizationalUnit->getCity(),
				$organizationalUnit->getPostCode(),
			),
			$organizationalUnit->getWebsite(),
			$organizationalUnit->getEmail(),
		);
	}

}
