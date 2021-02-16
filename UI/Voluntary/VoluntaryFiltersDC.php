<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Voluntary;

use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $key
 * @property-read bool $isAnySelected
 * @property-read bool $isFirstTimeAttendeesSelected
 * @property-read bool $isWeekendEventsSelected
 * @property-read bool $isHolidayEventsSelected
 * @property-read bool $isOneDayEventsSelected
 * @property-read bool $isSightsSelected
 * @property-read bool $isNatureSelected
 */
final class VoluntaryFiltersDC
{
	use PropertyHandler;

	private string $key;
	private bool $isAnySelected = false;
	private bool $isFirstTimeAttendeesSelected = false;
	private bool $isWeekendEventsSelected = false;
	private bool $isOneDayEventsSelected = false;
	private bool $isHolidayEventsSelected = false;
	private bool $isNatureSelected = false;
	private bool $isSightsSelected = false;

	private function __construct(string $key, ?string $selectedFilter = null)
	{
		$this->key = $key;

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

	public static function from(string $key, ?string $selectedFilter = null): static
	{
		return new static($key, $selectedFilter);
	}

}
