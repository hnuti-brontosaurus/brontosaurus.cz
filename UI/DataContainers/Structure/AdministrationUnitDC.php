<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Structure;

use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\AdministrationUnit;
use HnutiBrontosaurus\Theme\CoordinatesResolver\Coordinates;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $name
 * @property-read CoordinatesDC $coordinates
 * @property-read string $address
 * @property-read string $chairman
 * @property-read string $website
 * @property-read string $emailAddress
 * @property-read bool $isOfTypeClub
 * @property-read bool $isOfTypeBase
 * @property-read bool $isOfTypeRegional
 * @property-read bool $isOfTypeOffice
 * @property-read bool $isOfTypeChildren
 */
final class AdministrationUnitDC implements \JsonSerializable
{

	use PropertyHandler;


	private string $name;
	private CoordinatesDC $coordinates;
	private string $address;
	private ?string $chairman;
	private ?string $website;
	private ?string $emailAddress;
	private bool $isOfTypeClub = false;
	private bool $isOfTypeBase = false;
	private bool $isOfTypeRegional = false;
	private bool $isOfTypeOffice = false;
	private bool $ifOfTypeChildren = false;


	private function __construct(AdministrationUnit $organizationalUnit, Coordinates $coordinates)
	{
		$this->name = $organizationalUnit->getName();
		$this->coordinates = CoordinatesDC::fromDTO($coordinates);
		$this->address = $organizationalUnit->getAddress();
		$this->chairman = $organizationalUnit->getChairman();
		$this->website = $organizationalUnit->getWebsite();
		$this->emailAddress = $organizationalUnit->getEmail();

		$this->resolveUnitTypes($organizationalUnit);
	}


	public static function fromDTO(AdministrationUnit $administrationUnit, Coordinates $coordinates)
	{
		return new self($administrationUnit, $coordinates);
	}


	private function resolveUnitTypes(AdministrationUnit $administrationUnit): void
	{
		$this->isOfTypeChildren = $administrationUnit->getIsForKids();

		// All units including children units have set type coming through API. We let type setting only if we had not decided already that such unit is of children type.
		if ( ! $this->isOfTypeChildren) {
			$this->isOfTypeClub = $administrationUnit->isClub();
			$this->isOfTypeBase = $administrationUnit->isBaseUnit();
			$this->isOfTypeRegional = $administrationUnit->isRegionalUnit();
			$this->isOfTypeOffice = $administrationUnit->isOffice();
		}
	}


	public function jsonSerialize(): array
	{
		return [
			'name' => $this->name,
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
