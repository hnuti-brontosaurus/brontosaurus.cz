<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;


final readonly class Label
{

	public bool $hasSelectorModifier;

	public function __construct(
		public string $label,
		public ?string $selectorModifier = null,
	)
	{
		$this->hasSelectorModifier = $this->selectorModifier !== null;
	}

}
