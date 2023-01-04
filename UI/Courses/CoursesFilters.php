<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Courses;

use HnutiBrontosaurus\BisClient\Enums\EventCategory;
use HnutiBrontosaurus\BisClient\Request\Event\EventParameters;


final class CoursesFilters
{
	const FILTER_TALKS = 'prednasky';
	const FILTER_ORGANIZING = 'organizatorske-kurzy';
	const FILTER_THEMATIC = 'tematicke-kurzy';

	private static EventParameters $parameters;


	public static function apply(?string $selectedFilter, EventParameters $parameters)
	{
		self::$parameters = $parameters;

		if ($selectedFilter === null) {
			self::none();
		}

		switch ($selectedFilter) {
			case self::FILTER_TALKS:
				$parameters->setCategories([
					EventCategory::EDUCATIONAL_LECTURE(),
					EventCategory::CLUB_TALK(),
				]);
				break;

			case self::FILTER_ORGANIZING:
				$parameters->setCategory(EventCategory::EDUCATIONAL_OHB());
				break;

			case self::FILTER_THEMATIC:
				$parameters->setCategory(EventCategory::EDUCATIONAL_COURSE());
				break;
		}
	}


	private static function none(): void
	{
		self::allRelevantTypes();
	}


	private static function allRelevantTypes(): void
	{
		self::$parameters->setCategories([
			EventCategory::EDUCATIONAL_LECTURE(),
			EventCategory::EDUCATIONAL_COURSE(),
			EventCategory::EDUCATIONAL_OHB(),
			EventCategory::EDUCATIONAL_PROGRAM(),
			EventCategory::EDUCATIONAL_PROGRAM_WITH_STAY(),
			EventCategory::CLUB_TALK(),
		]);
	}

}
