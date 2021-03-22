<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Contacts;

use HnutiBrontosaurus\Theme\UI\PropertyHandler;


final class ContactDC
{
	use PropertyHandler;

	/**
	 * @param string[] $emailAddresses
	 */
	private function __construct(
		private string $name,
		private bool $hasPhoto,
		private ?string $photo,
		private string $role,
		private string $about,
		private array $emailAddresses,
	) {}

	/**
	 * @param string[] $emailAddresses
	 */
	public static function from(
		string $name,
		bool $hasPhoto,
		?string $photo,
		string $role,
		string $about,
		array $emailAddresses,
	): self
	{
		return new self($name, $hasPhoto, $photo, $role, $about, $emailAddresses);
	}
}
