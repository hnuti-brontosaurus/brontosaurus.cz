<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\OrganizationalUnit;

use Grifart\Enum\AutoInstances;
use Grifart\Enum\Enum;


/**
 * @method static OrganizationalUnitType CLUB()
 * @method static OrganizationalUnitType BASE()
 * @method static OrganizationalUnitType REGIONAL()
 * @method static OrganizationalUnitType OFFICE()
 */
final class OrganizationalUnitType extends Enum
{
	use AutoInstances;

	protected const CLUB = 1;
	protected const BASE = 2;
	protected const REGIONAL = 3;
	protected const OFFICE = 4;
}
