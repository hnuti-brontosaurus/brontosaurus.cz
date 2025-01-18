<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Structure;

use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\AdministrationUnit as AdministrationUnitFromClient;
use HnutiBrontosaurus\Theme\CoordinatesResolver\Coordinates;
use HnutiBrontosaurus\Theme\CoordinatesResolver\CoordinatesResolver;


final class AdministrationUnit implements \JsonSerializable
{

	private function __construct(
		private string $name,
		private ?string $description,
		private ?string $image,
		private Coordinates $coordinates,
		private string $address,
		private ?string $chairman,
		private ?string $website,
		private ?string $emailAddress,
		private bool $isOfTypeClub,
		private bool $isOfTypeBase,
		private bool $isOfTypeRegional,
		private bool $isOfTypeOffice,
		private bool $isOfTypeChildren,
	) {}


	public static function from(
		AdministrationUnitFromClient $administrationUnit,
		CoordinatesResolver $coordinatesResolver,
	): self
	{
		return new self(
			name: $administrationUnit->getName(),
			description: $administrationUnit->getDescription(),
			image: $administrationUnit->getImage()?->getMediumSizePath(),
			coordinates: $coordinatesResolver->resolve($administrationUnit),
			address: $administrationUnit->getAddress(),
			chairman: $administrationUnit->getChairman(),
			website: $administrationUnit->getWebsite(),
			emailAddress: $administrationUnit->getEmail(),
			isOfTypeClub: ! $administrationUnit->getIsForKids() && $administrationUnit->isClub(),
			isOfTypeBase: ! $administrationUnit->getIsForKids() && $administrationUnit->isBaseUnit(),
			isOfTypeRegional: ! $administrationUnit->getIsForKids() && $administrationUnit->isRegionalUnit(),
			isOfTypeOffice: ! $administrationUnit->getIsForKids() && $administrationUnit->isOffice(),
			isOfTypeChildren: $administrationUnit->getIsForKids(),
		);
	}


	public function jsonSerialize(): array
	{
		return [
			'name' => $this->name,
			'description' => $this->description,
			'image' => $this->image,
			'lat' => $this->coordinates->getLatitude(),
			'lng' => $this->coordinates->getLongitude(),
			'address' => $this->address,
			'chairman' => $this->chairman,
			'website' => $this->website,
			'email' => $this->emailAddress,
			'isOfTypeClub' => $this->isOfTypeClub,
			'isOfTypeBase' => $this->isOfTypeBase,
			'isOfTypeRegional' => $this->isOfTypeRegional,
			'isOfTypeOffice' => $this->isOfTypeOffice,
			'isOfTypeChildren' => $this->isOfTypeChildren,
		];
	}

}
