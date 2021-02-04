<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Structure;

use Grifart\GeocodingClient\Location;
use HnutiBrontosaurus\BisApiClient\Response\OrganizationalUnit\OrganizationalUnit;
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
final class OrganizationalUnitDC
{

	use PropertyHandler;


	/** @var string */
	private $name;

	/** @var CoordinatesDC */
	private $coordinates;

	/** @var AddressDC */
	private $address;

	/** @var string|NULL */
	private $chairman;

	/** @var string|NULL */
	private $website;

	/** @var string|NULL */
	private $emailAddress;

	/** @var bool */
	private $isOfTypeClub = FALSE;

	/** @var bool */
	private $isOfTypeBase = FALSE;

	/** @var bool */
	private $isOfTypeRegional = FALSE;

	/** @var bool */
	private $isOfTypeOffice = FALSE;

	/** @var bool */
	private $ifOfTypeChildren = FALSE;


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

}
