<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Courses;

use HnutiBrontosaurus\BisClient\Event\Category;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;


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
					Category::EDUCATIONAL_LECTURE(),
					Category::CLUB_LECTURE(),
				]);
				break;

			case self::FILTER_ORGANIZING:
				$parameters->setCategory(Category::EDUCATIONAL_OHB());
				break;

			case self::FILTER_THEMATIC:
				$parameters->setCategory(Category::EDUCATIONAL_COURSE());
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
			Category::EDUCATIONAL_LECTURE(),
			Category::EDUCATIONAL_COURSE(),
			Category::EDUCATIONAL_OHB(),
			Category::EDUCATIONAL_EDUCATIONAL(),
			Category::EDUCATIONAL_EDUCATIONAL_WITH_STAY(),
			Category::CLUB_LECTURE(),
		]);
	}

}
