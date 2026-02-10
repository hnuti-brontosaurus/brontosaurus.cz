<?php

namespace HnutiBrontosaurus\Theme\Filters;

use HnutiBrontosaurus\BisClient\Event\Category;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;


final class CoursesFilters
{
	const FILTER_ORGANIZING = 'organizatorske-kurzy';
	const FILTER_THEMATIC = 'tematicke-kurzy-a-prednasky';

	private static EventParameters $parameters;


	public static function apply(?string $selectedFilter, EventParameters $parameters)
	{
		self::$parameters = $parameters;

		if ($selectedFilter === null) {
			self::none();
		}

		switch ($selectedFilter) {
			case self::FILTER_ORGANIZING:
				$parameters->setCategory(Category::INTERNAL_EDUCATIONAL());
				break;

			case self::FILTER_THEMATIC:
				$parameters->setCategory(Category::PUBLIC_EDUCATIONAL());
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
			Category::INTERNAL_EDUCATIONAL(),
			Category::PUBLIC_EDUCATIONAL(),
		]);
	}

}
