<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation;


final class Invitation
{

	private function __construct(
		private string $introduction,
		private string $organizationalInformation,
		private ?string $accommodation,
		private Food $food,
		private ?string $workDescription,
		private ?int $workHoursPerDay,
		private ?Presentation $presentation,
	) {}


	public static function from(
		string $introduction,
		string $organizationalInformation,
		?string $accommodation,
		Food $food,
		?string $workDescription,
		?int $workHoursPerDay,
		?Presentation $presentation,
	): self
	{
		return new self(
			$introduction,
			$organizationalInformation,
			$accommodation,
			$food,
			$workDescription,
			$workHoursPerDay,
			$presentation,
		);
	}


	public function getIntroduction(): string
	{
		return $this->introduction;
	}


	public function getOrganizationalInformation(): string
	{
		return $this->organizationalInformation;
	}


	public function isAccommodationListed(): bool
	{
		return $this->accommodation !== null;
	}


	public function getAccommodation(): ?string
	{
		return $this->accommodation;
	}


	public function getFood(): Food
	{
		return $this->food;
	}


	public function getWorkDescription(): ?string
	{
		return $this->workDescription;
	}


	public function areWorkHoursPerDayListed(): bool
	{
		return $this->workHoursPerDay !== null;
	}


	public function getWorkHoursPerDay(): ?int
	{
		return $this->workHoursPerDay;
	}


	public function hasPresentation(): bool
	{
		return $this->presentation !== null;
	}


	public function getPresentation(): ?Presentation
	{
		return $this->presentation;
	}

}
