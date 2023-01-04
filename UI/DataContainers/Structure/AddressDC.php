<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Structure;

use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $street
 * @property-read string $city
 * @property-read string $postCode
 */
final class AddressDC
{
	use PropertyHandler;


	private function __construct(
		private string $street,
		private string $city,
		private string $postCode,
	) {}


	public static function from(string $street, string $city, string $postCode): self
	{
		return new self($street, $city, $postCode);
	}

}
