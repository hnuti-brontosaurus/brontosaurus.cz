<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Meetups;

use HnutiBrontosaurus\BisClient\Event\Category;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;


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
			Category::CLUB_MEETING(),
			Category::CLUB_LECTURE(),
			Category::FOR_PUBLIC(),
			Category::ECO_TENT(),
			Category::EXHIBITION(),
			Category::INTERNAL_VOLUNTEER_MEETING(),
		]);
	}

}
