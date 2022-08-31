<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event;

use Grifart\Enum\AutoInstances;
use Grifart\Enum\Enum;


/**
 * @method static ProgramType NONE()
 * @method static ProgramType NATURE()
 * @method static ProgramType SIGHTS()
 * @method static ProgramType BRDO()
 * @method static ProgramType EKOSTAN()
 * @method static ProgramType PSB()
 * @method static ProgramType EDUCATION()
 */
final class ProgramType extends Enum
{
	use AutoInstances;

	protected const NONE = 'none';
	protected const NATURE = 'ap';
	protected const SIGHTS = 'pamatky';
	protected const BRDO = 'brdo';
	protected const EKOSTAN = 'ekostan';
	protected const PSB = 'psb';
	protected const EDUCATION = 'vzdelavani';
}
