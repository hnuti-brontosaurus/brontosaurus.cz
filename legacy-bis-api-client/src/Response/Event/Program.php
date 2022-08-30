<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event;


final class Program
{

	private function __construct(
		private ProgramType $slug,
		private ?string $name,
	) {}


	public static function from(
		ProgramType $slug,
		?string $name,
	): self
	{
		return new self($slug, $name);
	}


	public function getSlug(): string
	{
		return $this->slug->toScalar();
	}


	public function getName(): ?string
	{
		return $this->name;
	}


	public function isNotSelected(): bool
	{
		return $this->slug->equals(ProgramType::NONE());
	}


	public function isOfTypeNature(): bool
	{
		return $this->slug->equals(ProgramType::NATURE());
	}


	public function isOfTypeSights(): bool
	{
		return $this->slug->equals(ProgramType::SIGHTS());
	}


	public function isOfTypeBrdo(): bool
	{
		return $this->slug->equals(ProgramType::BRDO());
	}


	public function isOfTypeEkostan(): bool
	{
		return $this->slug->equals(ProgramType::EKOSTAN());
	}


	public function isOfTypePsb(): bool
	{
		return $this->slug->equals(ProgramType::PSB());
	}


	public function isOfTypeEducation(): bool
	{
		return $this->slug->equals(ProgramType::EDUCATION());
	}

}
