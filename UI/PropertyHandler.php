<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI;

use Nette\Utils\Strings;


trait PropertyHandler
{

	public function __get(string $property): mixed
	{
		if ( ! \property_exists($this, $property)) {
			throw new \LogicException(\sprintf('Property `%s` is not declared.', $property));
		}

		if (Strings::startsWith($property, '_')) {
			throw new \LogicException(\sprintf('Can not use property `%s` as it starts with underscore which implies inaccessibility from outside.', $property));
		}

		return $this->{$property};
	}

	public function __isset(string $name): bool
	{
		return \property_exists($this, $name);
	}

}
