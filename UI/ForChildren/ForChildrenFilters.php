<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\ForChildren;

use HnutiBrontosaurus\BisClient\Enums\EventCategory;
use HnutiBrontosaurus\BisClient\Enums\EventGroup;
use HnutiBrontosaurus\BisClient\Enums\IntendedFor;
use HnutiBrontosaurus\BisClient\Request\Event\EventParameters;


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
				self::$parameters->setGroup(EventGroup::CAMP());
				self::allRelevantTargetGroups();
				break;

			case self::FILTER_UNITS:
				self::$parameters->setCategory(EventCategory::INTERNAL_GROUP_MEETING());
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
