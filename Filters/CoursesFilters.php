<?php

namespace HnutiBrontosaurus\Theme\Filters;

use HnutiBrontosaurus\BisClient\Event\Category;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;


final class CoursesFilters
{
	const Experiental = 'zazitkove';
	const Educational = 'vzdelavaci';
	const Singleday = 'jednodenni';
	const Multiday = 'vicedenni';

	private static EventParameters $parameters;


	public static function apply(?string $selectedFilter, EventParameters $parameters)
	{
		self::$parameters = $parameters;

		if ($selectedFilter === null) {
			self::none();
		}

		switch ($selectedFilter) {
			case self::Educational:
				$parameters->setCategories([
					Category::INTERNAL_EDUCATIONAL,
					Category::INTERNAL_EDUCATIONAL_FULL,
					Category::PUBLIC_EDUCATIONAL
				]);
				break;

			case self::Experiental:
				$parameters->setCategories([Category::EXPERIENTAL]);
				break;

			case self::Singleday:
				self::allRelevantTypes();
				break;

			case self::Multiday:
				self::allRelevantTypes();
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
			Category::EXPERIENTAL,
			Category::INTERNAL_EDUCATIONAL,
			Category::INTERNAL_EDUCATIONAL_FULL,
			Category::PUBLIC_EDUCATIONAL,
			Category::PRESENTATION,
		]);
	}

}
