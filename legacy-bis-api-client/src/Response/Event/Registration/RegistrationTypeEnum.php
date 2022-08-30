<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Registration;

use Grifart\Enum\AutoInstances;
use Grifart\Enum\Enum;


/**
 * @method static RegistrationTypeEnum BRONTOWEB()
 * @method static RegistrationTypeEnum EMAIL()
 * @method static RegistrationTypeEnum EXTERNAL_WEBPAGE()
 * @method static RegistrationTypeEnum NONE()
 * @method static RegistrationTypeEnum DISABLED()
 */
final class RegistrationTypeEnum extends Enum
{
	use AutoInstances;

	protected const BRONTOWEB = 1;
	protected const EMAIL = 2;
	protected const EXTERNAL_WEBPAGE = 3;
	protected const NONE = 4;
	protected const DISABLED = 5;
}
