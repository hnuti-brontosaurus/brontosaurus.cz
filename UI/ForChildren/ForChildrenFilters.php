<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\ForChildren;

use HnutiBrontosaurus\LegacyBisApiClient\Request\EventParameters;


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
				self::$parameters->setFilter(EventParameters::FILTER_CAMP);
				self::allRelevantTargetGroups();
				break;

			case self::FILTER_UNITS:
				self::$parameters->setType(EventParameters::TYPE_GROUP_MEETING);
				self::allRelevantTargetGroups();
				break;

			case self::FILTER_EVENTS:
				self::$parameters->setTargetGroup(EventParameters::TARGET_GROUP_CHILDREN);
				break;

			case self::FILTER_EVENTS_WITH_PARENTS:
				self::$parameters->setTargetGroup(EventParameters::TARGET_GROUP_FAMILIES);
				break;
		}
	}


	private static function none(): void
	{
		self::allRelevantTargetGroups();
	}


	private static function allRelevantTargetGroups(): void
	{
		self::$parameters->setTargetGroups([
			EventParameters::TARGET_GROUP_CHILDREN,
			EventParameters::TARGET_GROUP_FAMILIES,
		]);
	}

}
