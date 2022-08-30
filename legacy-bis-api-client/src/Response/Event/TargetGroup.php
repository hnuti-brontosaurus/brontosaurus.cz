<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event;

use HnutiBrontosaurus\LegacyBisApiClient\InvalidArgumentException;


final class TargetGroup
{

	const EVERYONE = 1;
	const ADULTS = 2;
	const CHILDREN = 3;
	const FAMILIES = 4;
	const FIRST_TIME_ATTENDEES = 5;


	private int $id;

	private function __construct(int $id)
	{
		if ( ! \in_array($id, [
			self::EVERYONE,
			self::ADULTS,
			self::CHILDREN,
			self::FAMILIES,
			self::FIRST_TIME_ATTENDEES,
		], true)) {
			throw new InvalidArgumentException('Value `' . $id . '` is not of valid types for `id` parameter.');
		}

		$this->id = $id;
	}


	public static function from(int $id): self
	{
		return new self($id);
	}



	public function isOfTypeEveryone(): bool
	{
		return $this->id === self::EVERYONE;
	}


	public function isOfTypeAdults(): bool
	{
		return $this->id === self::ADULTS;
	}


	public function isOfTypeChildren(): bool
	{
		return $this->id === self::CHILDREN;
	}


	public function isOfTypeFamilies(): bool
	{
		return $this->id === self::FAMILIES;
	}


	public function isOfTypeFirstTimeAttendees(): bool
	{
		return $this->id === self::FIRST_TIME_ATTENDEES;
	}

}
