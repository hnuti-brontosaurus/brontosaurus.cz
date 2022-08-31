<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation;


final class Photo
{

	private function __construct(
		private string $path,
	) {}


	public static function from(string $path): self
	{
		return new self($path);
	}


	public function getPath(): string
	{
		return $this->path;
	}

}
