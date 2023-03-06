<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;

use function str_replace;


final class ApplicationUrlTemplate
{
	private function __construct(
		private string $urlTemplate,
	) {}

	public static function from(string $urlTemplate): self
	{
		return new self($urlTemplate);
	}

	public function for(int $eventId): string
	{
		return str_replace('{ID}', (string) $eventId, $this->urlTemplate);
	}

}
