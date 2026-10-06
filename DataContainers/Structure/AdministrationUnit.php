<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Structure;

use HnutiBrontosaurus\BisClient\AdministrationUnit\Category;
use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\AdministrationUnit as AdministrationUnitFromClient;
use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\SubUnit as SubUnitFromClient;
use HnutiBrontosaurus\BisClient\Response\Coordinates;

final readonly class AdministrationUnit implements \JsonSerializable
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
			name: $administrationUnit->name,
			parent: null,
			description: $administrationUnit->description,
			image: $administrationUnit->image?->mediumSizePath,
			coordinates: $administrationUnit->coordinates,
			address: $administrationUnit->address,
			chairman: $administrationUnit->chairman,
			website: $administrationUnit->website,
			emailAddress: $administrationUnit->email,
			isOfTypeClub: ! $administrationUnit->isForKids && $administrationUnit->category === Category::CLUB,
			isOfTypeBase: ! $administrationUnit->isForKids && $administrationUnit->category === Category::BASIC_SECTION,
			isOfTypeRegional: ! $administrationUnit->isForKids && $administrationUnit->category === Category::REGIONAL_CENTER,
			isOfTypeOffice: ! $administrationUnit->isForKids && $administrationUnit->category === Category::HEADQUARTER,
			isOfTypeChildren: $administrationUnit->isForKids,
		);
	}


	public static function fromSubUnit(SubUnitFromClient $subUnit, string $parentName): self
	{
		return new self(
			name: $subUnit->name,
			parent: $parentName,
			description: $subUnit->description,
			image: null,
			coordinates: $subUnit->coordinates,
			address: $subUnit->address,
			chairman: $subUnit->mainLeader,
			website: $subUnit->website,
			emailAddress: $subUnit->email,
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
			'lat' => $this->coordinates->latitude,
			'lng' => $this->coordinates->longitude,
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
