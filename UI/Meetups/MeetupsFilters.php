<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Meetups;

use HnutiBrontosaurus\BisClient\Enums\EventCategory;
use HnutiBrontosaurus\BisClient\Request\Event\EventParameters;


final class MeetupsFilters
{

	private static EventParameters $parameters;


	public static function apply(?string $selectedFilter, EventParameters $parameters): void
	{
		self::$parameters = $parameters;
		self::none(); // no filters yet
	}


	private static function none(): void
	{
		self::allRelevantTypes();
	}


	private static function allRelevantTypes(): void
	{
		self::$parameters->setCategories([
			EventCategory::CLUB_MEETUP(),
			EventCategory::CLUB_TALK(),
			EventCategory::FOR_PUBLIC(),
			EventCategory::EKOSTAN(),
			EventCategory::EXHIBITION(),
			EventCategory::INTERNAL_VOLUNTEER_MEETING(),
		]);
	}

}
