<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\EventDetail\DTO;


final class ApplicationFormAdditionalQuestion
{

	private function __construct(
		private string $question,
		private string $answer,
	) {}


	public static function from(string $question, string $answer): self
	{
		return new self($question, \trim($answer));
	}


	public function getQuestion(): string
	{
		return $this->question;
	}


	public function getAnswer(): string
	{
		return $this->answer;
	}


	public function toString(): string
	{
		return $this->question . ' ' . $this->answer;
	}


	public function __toString(): string
	{
		return $this->toString();
	}

}
