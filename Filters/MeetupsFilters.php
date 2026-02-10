<?php

namespace HnutiBrontosaurus\Theme\Filters;

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
			Category::PRESENTATION(),
		]);
	}

}
