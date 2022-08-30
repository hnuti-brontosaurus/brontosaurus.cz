<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Courses;

use HnutiBrontosaurus\LegacyBisApiClient\Request\EventParameters;


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
				$parameters->setTypes([
					EventParameters::TYPE_EDUCATIONAL_TALK,
					EventParameters::TYPE_CLUB_TALK,
				]);
				break;

			case self::FILTER_ORGANIZING:
				$parameters->setType(EventParameters::TYPE_EDUCATIONAL_OHB);
				break;

			case self::FILTER_THEMATIC:
				$parameters->setType(EventParameters::TYPE_EDUCATIONAL_COURSES);
				break;
		}
	}


	private static function none(): void
	{
		self::allRelevantTypes();
	}


	private static function allRelevantTypes(): void
	{
		self::$parameters->setTypes([
			EventParameters::TYPE_EDUCATIONAL_TALK,
			EventParameters::TYPE_EDUCATIONAL_COURSES,
			EventParameters::TYPE_EDUCATIONAL_OHB,
			EventParameters::TYPE_LEARNING_PROGRAM,
			EventParameters::TYPE_RESIDENTIAL_LEARNING_PROGRAM,
			EventParameters::TYPE_CLUB_TALK,
		]);
	}

}
