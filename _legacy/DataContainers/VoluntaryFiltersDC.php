<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\Theme\Filters\VoluntaryFilters;


final class VoluntaryFiltersDC
{

	private function __construct(
		public readonly bool $isAnySelected,
		public readonly bool $isFirstTimeAttendeesSelected,
		public readonly bool $isWeekendEventsSelected,
		public readonly bool $isOneDayEventsSelected,
		public readonly bool $isHolidayEventsSelected,
		public readonly bool $isNatureSelected,
		public readonly bool $isSightsSelected,
	) {}

	public static function from(?string $selectedFilter = null): self
	{
		$isAnySelected = false;
		$isFirstTimeAttendeesSelected = false;
		$isWeekendEventsSelected = false;
		$isOneDayEventsSelected = false;
		$isHolidayEventsSelected = false;
		$isNatureSelected = false;
		$isSightsSelected = false;

		if ($selectedFilter === null) {
			return new self(
				$isAnySelected,
				$isFirstTimeAttendeesSelected,
				$isWeekendEventsSelected,
				$isOneDayEventsSelected,
				$isHolidayEventsSelected,
				$isNatureSelected,
				$isSightsSelected,
			);
		}

		$isAnySelected = true;

		switch ($selectedFilter) {
			case VoluntaryFilters::FILTER_FIRST_TIME:
				$isFirstTimeAttendeesSelected = true;
				break;

			case VoluntaryFilters::FILTER_WEEKEND_EVENTS:
				$isWeekendEventsSelected = true;
				break;

			case VoluntaryFilters::FILTER_ONE_DAY_EVENTS:
				$isOneDayEventsSelected = true;
				break;

			case VoluntaryFilters::FILTER_HOLIDAY_EVENTS:
				$isHolidayEventsSelected = true;
				break;

			case VoluntaryFilters::FILTER_NATURE:
				$isNatureSelected = true;
				break;

			case VoluntaryFilters::FILTER_SIGHTS:
				$isSightsSelected = true;
				break;

			default:
				$isAnySelected = false;
				break;
		}

		return new self(
			$isAnySelected,
			$isFirstTimeAttendeesSelected,
			$isWeekendEventsSelected,
			$isOneDayEventsSelected,
			$isHolidayEventsSelected,
			$isNatureSelected,
			$isSightsSelected,
		);
	}

}
