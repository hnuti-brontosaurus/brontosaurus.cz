<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Voluntary;

use HnutiBrontosaurus\LegacyBisApiClient\Request\EventParameters;


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
				self::$parameters->setTargetGroup(EventParameters::TARGET_GROUP_FIRST_TIME_ATTENDEES);
				break;

			// these two are post-resolved by their day counts
			case self::FILTER_WEEKEND_EVENTS:
			case self::FILTER_ONE_DAY_EVENTS:
				self::allRelevantTypes();
				self::allRelevantPrograms();
				self::allRelevantPrograms();
				break;

			case self::FILTER_HOLIDAY_EVENTS:
				self::allRelevantTypes();
				self::allRelevantTargetGroups();
				self::$parameters->setProgram(EventParameters::PROGRAM_PSB);
				break;

			case self::FILTER_NATURE:
				self::allRelevantTypes();
				self::allRelevantTargetGroups();
				self::$parameters->setProgram(EventParameters::PROGRAM_NATURE);
				break;

			case self::FILTER_SIGHTS:
				self::allRelevantTypes();
				self::allRelevantTargetGroups();
				self::$parameters->setProgram(EventParameters::PROGRAM_SIGHTS);
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
		self::$parameters->setTypes([
			EventParameters::TYPE_VOLUNTARY,
			EventParameters::TYPE_EXPERIENCE,
			EventParameters::TYPE_SPORT,
		]);
	}

	private static function allRelevantPrograms(): void
	{
		self::$parameters->setPrograms([
			EventParameters::PROGRAM_NOT_SELECTED,
			EventParameters::PROGRAM_NATURE,
			EventParameters::PROGRAM_SIGHTS,
			EventParameters::PROGRAM_PSB,
		]);
	}

	private static function allRelevantTargetGroups(): void
	{
		self::$parameters->setTargetGroups([
			EventParameters::TARGET_GROUP_EVERYONE,
			EventParameters::TARGET_GROUP_ADULTS,
			EventParameters::TARGET_GROUP_FIRST_TIME_ATTENDEES,
		]);
	}

}
