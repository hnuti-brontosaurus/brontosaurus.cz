<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Structure;

use HnutiBrontosaurus\BisClient\AdministrationUnit\Category;
use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\AdministrationUnit as AdministrationUnitFromClient;
use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\SubUnit as SubUnitFromClient;
use HnutiBrontosaurus\BisClient\Response\Coordinates;

final class AdministrationUnit implements \JsonSerializable
{

	private function __construct(
		private string $name,
		private ?string $parent,
		private ?string $description,
		private ?string $image,
		private Coordinates $coordinates,
		private ?string $address,
		private ?string $chairman,
		private ?string $website,
		private ?string $emailAddress,
		private bool $isOfTypeClub,
		private bool $isOfTypeBase,
		private bool $isOfTypeRegional,
		private bool $isOfTypeOffice,
		private bool $isOfTypeChildren,
	) {}


	public static function fromUnit(AdministrationUnitFromClient $administrationUnit): self
	{
		return new self(
			name: $administrationUnit->getName(),
			parent: null,
			description: $administrationUnit->getDescription(),
			image: $administrationUnit->getImage()?->getMediumSizePath(),
			coordinates: $administrationUnit->getCoordinates(),
			address: $administrationUnit->getAddress(),
			chairman: $administrationUnit->getChairman(),
			website: $administrationUnit->getWebsite(),
			emailAddress: $administrationUnit->getEmail(),
			isOfTypeClub: ! $administrationUnit->getIsForKids() && $administrationUnit->getCategory() === Category::CLUB,
			isOfTypeBase: ! $administrationUnit->getIsForKids() && $administrationUnit->getCategory() === Category::BASIC_SECTION,
			isOfTypeRegional: ! $administrationUnit->getIsForKids() && $administrationUnit->getCategory() === Category::REGIONAL_CENTER,
			isOfTypeOffice: ! $administrationUnit->getIsForKids() && $administrationUnit->getCategory() === Category::HEADQUARTER,
			isOfTypeChildren: $administrationUnit->getIsForKids(),
		);
	}


	public static function fromSubUnit(SubUnitFromClient $subUnit, string $parentName): self
	{
		return new self(
			name: $subUnit->getName(),
			parent: $parentName,
			description: $subUnit->getDescription(),
			image: null,
			coordinates: $subUnit->getCoordinates(),
			address: $subUnit->getAddress(),
			chairman: $subUnit->getMainLeader(),
			website: $subUnit->getWebsite(),
			emailAddress: $subUnit->getEmail(),
			isOfTypeClub: false,
			isOfTypeBase: false,
			isOfTypeRegional: false,
			isOfTypeOffice: false,
			isOfTypeChildren: true,
		);
	}


	public function jsonSerialize(): array
	{
		return [
			'name' => $this->name,
			'parent' => $this->parent,
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
