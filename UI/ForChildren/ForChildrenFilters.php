<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\ForChildren;

use HnutiBrontosaurus\BisClient\Event\Category;
use HnutiBrontosaurus\BisClient\Event\Group;
use HnutiBrontosaurus\BisClient\Event\IntendedFor;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;


final class ForChildrenFilters
{

	const FILTER_CAMPS = 'detske-tabory';
	const FILTER_UNITS = 'detske-oddily';
	const FILTER_EVENTS = 'akce-pro-deti';
	const FILTER_EVENTS_WITH_PARENTS = 'akce-pro-rodice-s-detmi';


	private static EventParameters $parameters;


	public static function apply(?string $selectedFilter, EventParameters $parameters): void
	{
		self::$parameters = $parameters;

		if ($selectedFilter === null) {
			self::none();
		}

		switch ($selectedFilter) {
			case self::FILTER_CAMPS:
				self::$parameters->setGroup(Group::CAMP());
				self::allRelevantTargetGroups();
				break;

			case self::FILTER_UNITS:
				self::$parameters->setCategory(Category::INTERNAL_SECTION_MEETING());
				self::allRelevantTargetGroups();
				break;

			case self::FILTER_EVENTS:
				self::$parameters->setIntendedFor(IntendedFor::KIDS());
				break;

			case self::FILTER_EVENTS_WITH_PARENTS:
				self::$parameters->setIntendedFor(IntendedFor::PARENTS_WITH_KIDS());
				break;
		}
	}


	private static function none(): void
	{
		self::allRelevantTargetGroups();
	}


	private static function allRelevantTargetGroups(): void
	{
		self::$parameters->setMultipleIntendedFor([
			IntendedFor::KIDS(),
			IntendedFor::PARENTS_WITH_KIDS(),
		]);
	}

}
