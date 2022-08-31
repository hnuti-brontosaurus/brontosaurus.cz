<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Registration;


final class RegistrationQuestion
{

	private function __construct(
		private string $question,
	) {}


	public static function from(string $question): self
	{
		return new self($question);
	}


	public function toString(): string
	{
		return $this->question;
	}


	public function __toString(): string
	{
		return $this->toString();
	}

}
