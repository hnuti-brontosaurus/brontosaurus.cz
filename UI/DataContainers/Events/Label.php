<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $label
 * @property-read bool $hasSelectorModifier
 * @property-read ?string $selectorModifier
 */
final class Label
{
	use PropertyHandler;

	private bool $hasSelectorModifier;

	public function __construct(
		private string $label,
		private ?string $selectorModifier = null,
	)
	{
		$this->hasSelectorModifier = $this->selectorModifier !== null;
	}

}
