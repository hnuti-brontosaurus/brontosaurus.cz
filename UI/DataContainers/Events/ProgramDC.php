<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Enums\Program;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read bool $isOfTypeNature
 * @property-read bool $isOfTypeSights
 * @property-read bool $isOfTypePsb
 */
final class ProgramDC
{
	use PropertyHandler;

	private bool $isOfTypeNature;
	private bool $isOfTypeSights;
	private bool $isOfTypePsb;


	public function __construct(Program $program)
	{
		$this->isOfTypeNature = $program->equals(Program::NATURE());
		$this->isOfTypeSights = $program->equals(Program::MONUMENTS());
		$this->isOfTypePsb = $program->equals(Program::HOLIDAYS_WITH_BRONTOSAURUS());
	}

}
