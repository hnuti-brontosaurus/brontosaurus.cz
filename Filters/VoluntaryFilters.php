<?php

namespace HnutiBrontosaurus\Theme\Filters;

use HnutiBrontosaurus\BisClient\Event\Category;
use HnutiBrontosaurus\BisClient\Event\Group;
use HnutiBrontosaurus\BisClient\Event\IntendedFor;
use HnutiBrontosaurus\BisClient\Event\Program;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;


final class VoluntaryFilters
{

	const FILTER_FIRST_TIME = 'jedu-poprve';
	const FILTER_WEEKEND_EVENTS = 'vikendovky';
	const FILTER_ONE_DAY_EVENTS = 'jednodenni';
	const FILTER_HOLIDAY_EVENTS = 'prazdninove';
	const FILTER_NATURE = 'priroda';
	const FILTER_SIGHTS = 'pamatky';

	private static EventParameters $parameters;


	public static function apply(?string $selectedFilter, EventParameters $parameters): void
	{
		self::$parameters = $parameters;

		if ($selectedFilter === null) {
			self::none();
		}

		switch ($selectedFilter) {
			case self::FILTER_FIRST_TIME:
				self::allRelevantTypes();
				self::allRelevantPrograms();
				self::$parameters->setIntendedFor(IntendedFor::FIRST_TIME_PARTICIPANT());
				break;

			case self::FILTER_WEEKEND_EVENTS:
				self::$parameters->setGroup(Group::WEEKEND_EVENT());
				self::allRelevantTypes();
				self::allRelevantPrograms();
				self::allRelevantTargetGroups();
				break;

			case self::FILTER_ONE_DAY_EVENTS:
				self::$parameters->setGroup(Group::OTHER()); // includes possibly also other events than camps and weekend events, is filtered later by day count
				self::allRelevantTypes();
				self::allRelevantPrograms();
				self::allRelevantTargetGroups();
				break;

			case self::FILTER_HOLIDAY_EVENTS:
				self::allRelevantTypes();
				self::allRelevantTargetGroups();
				self::$parameters->setProgram(Program::HOLIDAYS_WITH_BRONTOSAURUS());
				break;

			case self::FILTER_NATURE:
				self::allRelevantTypes();
				self::allRelevantTargetGroups();
				self::$parameters->setProgram(Program::NATURE());
				break;

			case self::FILTER_SIGHTS:
				self::allRelevantTypes();
				self::allRelevantTargetGroups();
				self::$parameters->setProgram(Program::MONUMENTS());
				break;
		}
	}


	private static function none(): void
	{
		self::allRelevantTypes();
		self::allRelevantPrograms();
		self::allRelevantTargetGroups();
	}


	private static function allRelevantTypes(): void
	{
		self::$parameters->setCategories([
			Category::VOLUNTEERING(),
			Category::EXPERIENTAL(),
		]);
	}

	private static function allRelevantPrograms(): void
	{
		self::$parameters->setPrograms([
			Program::NONE(),
			Program::NATURE(),
			Program::MONUMENTS(),
			Program::HOLIDAYS_WITH_BRONTOSAURUS(),
			Program::INTERNATIONAL(),
		]);
	}

	private static function allRelevantTargetGroups(): void
	{
		self::$parameters->setMultipleIntendedFor([
			IntendedFor::ALL(),
			IntendedFor::YOUNG_AND_ADULT(),
			IntendedFor::FIRST_TIME_PARTICIPANT(),
		]);
	}

}
