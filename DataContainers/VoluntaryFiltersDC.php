<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\Theme\Filters\VoluntaryFilters;


final class VoluntaryFiltersDC
{

	public readonly bool $isAnySelected;
	public readonly bool $isFirstTimeAttendeesSelected;
	public readonly bool $isWeekendEventsSelected;
	public readonly bool $isOneDayEventsSelected;
	public readonly bool $isHolidayEventsSelected;
	public readonly bool $isNatureSelected;
	public readonly bool $isSightsSelected;

	private function __construct(?string $selectedFilter = null)
	{
		$this->isAnySelected = false;
		$this->isFirstTimeAttendeesSelected = false;
		$this->isWeekendEventsSelected = false;
		$this->isOneDayEventsSelected = false;
		$this->isHolidayEventsSelected = false;
		$this->isNatureSelected = false;
		$this->isSightsSelected = false;

		if ($selectedFilter === null) {
			return;
		}

		$this->isAnySelected = true;

		switch ($selectedFilter) {
			case VoluntaryFilters::FILTER_FIRST_TIME:
				$this->isFirstTimeAttendeesSelected = true;
				break;

			case VoluntaryFilters::FILTER_WEEKEND_EVENTS:
				$this->isWeekendEventsSelected = true;
				break;

			case VoluntaryFilters::FILTER_ONE_DAY_EVENTS:
				$this->isOneDayEventsSelected = true;
				break;

			case VoluntaryFilters::FILTER_HOLIDAY_EVENTS:
				$this->isHolidayEventsSelected = true;
				break;

			case VoluntaryFilters::FILTER_NATURE:
				$this->isNatureSelected = true;
				break;

			case VoluntaryFilters::FILTER_SIGHTS:
				$this->isSightsSelected = true;
				break;

			default:
				$this->isAnySelected = false;
				break;
		}
	}

	public static function from(?string $selectedFilter = null): static
	{
		return new static($selectedFilter);
	}

}
