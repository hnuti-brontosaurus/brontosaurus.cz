<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event;


final class Place
{

	private function __construct(
		private string $name,
		private ?string $coordinates, // in format 49.132456 16.123456
	) {}


	public static function from(
		string $name,
		?string $coordinates,
	): self
	{
		// only `49.132456 16.123456` format is used by users right now
		if ($coordinates !== null && ! \preg_match('|[0-9]+(\.[0-9]+) [0-9]+(\.[0-9]+)|', $coordinates)) {
			$coordinates = null;
		}

		return new self($name, $coordinates);
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function areCoordinatesListed(): bool
	{
		return $this->coordinates !== null;
	}


	public function getCoordinates(): ?string
	{
		return $this->coordinates;
	}

}
