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


	/** @var bool */
	private $isOfTypeNature = false;

	/** @var bool */
	private $isOfTypeSights = false;

	/** @var bool */
	private $isOfTypePsb = false;


	public function __construct(Program $program)
	{
		$this->isOfTypeNature = $program->isOfTypeNature();
		$this->isOfTypeSights = $program->isOfTypeSights();
		$this->isOfTypePsb = $program->isOfTypePsb();
	}

}
