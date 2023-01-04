<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Program;
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
		$this->isOfTypeNature = $program->isOfTypeNature();
		$this->isOfTypeSights = $program->isOfTypeSights();
		$this->isOfTypePsb = $program->isOfTypePsb();
	}

}
