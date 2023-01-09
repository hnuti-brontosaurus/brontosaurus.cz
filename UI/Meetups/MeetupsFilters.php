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
			EventCategory::CLUB_MEETING(),
			EventCategory::CLUB_LECTURE(),
			EventCategory::FOR_PUBLIC(),
			EventCategory::ECO_TENT(),
			EventCategory::EXHIBITION(),
			EventCategory::INTERNAL_VOLUNTEER_MEETING(),
		]);
	}

}
