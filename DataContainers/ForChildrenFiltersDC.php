<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\Theme\Filters\ForChildrenFilters;


final class ForChildrenFiltersDC
{

	public readonly bool $isAnySelected;
	public readonly bool $isCampsSelected;
	public readonly bool $isUnitsSelected;
	public readonly bool $isEventsSelected;
	public readonly bool $isEventsWithParentsSelected;


	private function __construct(?string $selectedFilter = null)
	{
		$this->isAnySelected = false;
		$this->isCampsSelected = false;
		$this->isUnitsSelected = false;
		$this->isEventsSelected = false;
		$this->isEventsWithParentsSelected = false;

		if ($selectedFilter === null) {
			return;
		}

		$this->isAnySelected = true;

		switch ($selectedFilter) {
			case ForChildrenFilters::FILTER_CAMPS:
				$this->isCampsSelected = true;
				break;

			case ForChildrenFilters::FILTER_UNITS:
				$this->isUnitsSelected = true;
				break;

			case ForChildrenFilters::FILTER_EVENTS:
				$this->isEventsSelected = true;
				break;

			case ForChildrenFilters::FILTER_EVENTS_WITH_PARENTS:
				$this->isEventsWithParentsSelected = true;
				break;

			default:
				$this->isAnySelected = false;
				break;
		}
	}


	public static function from(?string $selectedFilter = null): self
	{
		return new self($selectedFilter);
	}

}
