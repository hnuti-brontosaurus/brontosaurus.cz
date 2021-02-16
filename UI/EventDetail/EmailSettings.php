<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\EventDetail;


final class EmailSettings
{

	private function __construct(
		private string $fromAddress,
		private string $fromName,
	) {}


	public static function from(string $fromAddress, string $fromName): self
	{
		return new self($fromAddress, $fromName);
	}


	public function getFromAddress(): string
	{
		return $this->fromAddress;
	}


	public function getFromName(): string
	{
		return $this->fromName;
	}

}
