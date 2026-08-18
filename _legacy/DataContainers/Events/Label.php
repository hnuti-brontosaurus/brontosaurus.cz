<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;


final class Label
{

	public readonly bool $hasSelectorModifier;

	public function __construct(
		public readonly string $label,
		public readonly ?string $selectorModifier = null,
	)
	{
		$this->hasSelectorModifier = $this->selectorModifier !== null;
	}

}
