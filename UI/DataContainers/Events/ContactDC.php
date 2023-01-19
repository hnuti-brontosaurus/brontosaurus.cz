<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Organizer;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $hasPerson
 * @property-read string $person
 * @property-read string $email
 * @property-read string $phone
 */
final class ContactDC
{
	use PropertyHandler;

	private function __construct(
		private bool $hasPerson,
		private ?string $person,
		private string $email,
		private string $phone,
	) {}


	public static function fromDTO(Organizer $organizer): self
	{
		return new self(
			$organizer->getContactPersonName() !== null,
			$organizer->getContactPersonName(),
			$organizer->getContactEmail(),
			$organizer->getContactPhone()
		);
	}

}
