<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Structure;

use Grifart\GeocodingClient\Location;
use HnutiBrontosaurus\LegacyBisApiClient\Response\OrganizationalUnit\OrganizationalUnit;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $name
 * @property-read CoordinatesDC $coordinates
 * @property-read AddressDC $address
 * @property-read string $chairman
 * @property-read string $website
 * @property-read string $emailAddress
 * @property-read bool $isOfTypeClub
 * @property-read bool $isOfTypeBase
 * @property-read bool $isOfTypeRegional
 * @property-read bool $isOfTypeOffice
 * @property-read bool $isOfTypeChildren
 */
final class OrganizationalUnitDC implements \JsonSerializable
{

	use PropertyHandler;


	private string $name;
	private CoordinatesDC $coordinates;
	private AddressDC $address;
	private ?string $chairman;
	private ?string $website;
	private ?string $emailAddress;
	private bool $isOfTypeClub = false;
	private bool $isOfTypeBase = false;
	private bool $isOfTypeRegional = false;
	private bool $isOfTypeOffice = false;
	private bool $ifOfTypeChildren = false;


	private function __construct(OrganizationalUnit $organizationalUnit, Location $location)
	{
		$this->name = $organizationalUnit->getName();
		$this->coordinates = CoordinatesDC::fromDTO($location);
		$this->address = AddressDC::from(
			$organizationalUnit->getStreet(),
			$organizationalUnit->getCity(),
			$organizationalUnit->getPostCode()
		);
		$this->chairman = $organizationalUnit->getChairman();
		$this->website = $organizationalUnit->getWebsite();
		$this->emailAddress = $organizationalUnit->getEmail();

		$this->resolveUnitTypes($organizationalUnit);
	}


	public static function fromDTO(OrganizationalUnit $organizationalUnit, Location $location)
	{
		return new self($organizationalUnit, $location);
	}


	/**
	 * Children units are not explicitly flagged in API so we have to find them in a different way.
	 * This simple algorithm relies on axiom (which is based on quick analysis and does not have to be true though)
	 * that all children units have "brdo" or "brďo" string in their name.
	 *
	 * @return void
	 */
	private function resolveUnitTypes(OrganizationalUnit $organizationalUnit)
	{
		$name = $organizationalUnit->getName();

		$isOfTypeChildren = (\mb_stripos($name, 'brdo') !== FALSE) || (\mb_stripos($name, 'brďo') !== FALSE); // Be aware of checking just `mb_stripos() || mb_stripos()` - if found string is on zero-th position, it checks `0 || 0` which means `FALSE || FALSE` in PHP because of auto type casting.
		$this->isOfTypeChildren = $isOfTypeChildren;

		// All units including children units have set type coming through API. We let type setting only if we had not decided already that such unit is of children type.
		if ( ! $isOfTypeChildren) {
			$this->isOfTypeClub = $organizationalUnit->isClub();
			$this->isOfTypeBase = $organizationalUnit->isBaseUnit();
			$this->isOfTypeRegional = $organizationalUnit->isRegionalUnit();
			$this->isOfTypeOffice = $organizationalUnit->isOffice();
		}
	}


	public function jsonSerialize(): array
	{
		return [
			'name' => $this->name,
			'lat' => $this->coordinates->latitude,
			'lng' => $this->coordinates->longitude,
			'address' => [
				'street' => $this->address->street,
				'postCode' => $this->address->postCode,
				'city' => $this->address->city,
			],
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
