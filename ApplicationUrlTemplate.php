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

	public function for(int $eventId, string $returnUrl): string
	{
		$url = $this->urlTemplate;
		$url = str_replace('{ID}', (string) $eventId, $url); // BC
		$url = str_replace('{id}', (string) $eventId, $url);
		$url = str_replace('{returnUrl}', $returnUrl, $url);
		return $url;
	}

}
