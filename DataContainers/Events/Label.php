<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\Theme\PropertyHandler;


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
