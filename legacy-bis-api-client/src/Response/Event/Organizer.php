<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event;


final class Organizer
{

	private function __construct(
		private ?OrganizerOrganizationalUnit $organizationalUnit,
		private ?string $responsiblePerson,
		private ?string $organizers,
		private ?string $contactPersonName,
		private string $contactPhone,
		private string $contactEmail,
	) {}


	public static function from(
		?OrganizerOrganizationalUnit $organizationalUnit,
		?string $responsiblePerson,
		?string $organizers,
		?string $contactPersonName,
		string $contactPhone,
		string $contactEmail,
	): self
	{
		return new self(
			$organizationalUnit,
			$responsiblePerson,
			$organizers,
			$contactPersonName,
			$contactPhone,
			$contactEmail,
		);
	}


	public function getOrganizationalUnit(): ?OrganizerOrganizationalUnit
	{
		return $this->organizationalUnit;
	}


	public function getResponsiblePerson(): ?string
	{
		return $this->responsiblePerson;
	}


	public function areOrganizersListed(): bool
	{
		return $this->organizers !== null;
	}


	public function getOrganizers(): ?string
	{
		return $this->organizers;
	}


	public function getContactPersonName(): ?string
	{
		return $this->contactPersonName;
	}


	public function getContactPhone(): string
	{
		return $this->contactPhone;
	}


	public function getContactEmail(): string
	{
		return $this->contactEmail;
	}

}
