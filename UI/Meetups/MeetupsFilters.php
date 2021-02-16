<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Meetups;

use HnutiBrontosaurus\BisApiClient\Request\EventParameters;


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
		self::$parameters->setTypes([
			EventParameters::TYPE_CLUB_MEETUP,
			EventParameters::TYPE_CLUB_TALK,
			EventParameters::TYPE_FOR_PUBLIC,
			EventParameters::TYPE_EKOSTAN,
			EventParameters::TYPE_EXHIBITION,
			EventParameters::TYPE_ACTION_GROUP,
		]);
	}

}
