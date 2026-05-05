<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\Theme\Filters\ForChildrenFilters;


final class ForChildrenFiltersDC
{

	private function __construct(
		public readonly bool $isAnySelected,
		public readonly bool $isCampsSelected,
		public readonly bool $isUnitsSelected,
		public readonly bool $isEventsSelected,
		public readonly bool $isEventsWithParentsSelected,
	) {}

	public static function from(?string $selectedFilter = null): self
	{
		$isAnySelected = false;
		$isCampsSelected = false;
		$isUnitsSelected = false;
		$isEventsSelected = false;
		$isEventsWithParentsSelected = false;

		if ($selectedFilter === null) {
			return new self(
				$isAnySelected,
				$isCampsSelected,
				$isUnitsSelected,
				$isEventsSelected,
				$isEventsWithParentsSelected,
			);
		}

		$isAnySelected = true;

		switch ($selectedFilter) {
			case ForChildrenFilters::FILTER_CAMPS:
				$isCampsSelected = true;
				break;

			case ForChildrenFilters::FILTER_UNITS:
				$isUnitsSelected = true;
				break;

			case ForChildrenFilters::FILTER_EVENTS:
				$isEventsSelected = true;
				break;

			case ForChildrenFilters::FILTER_EVENTS_WITH_PARENTS:
				$isEventsWithParentsSelected = true;
				break;

			default:
				$isAnySelected = false;
				break;
		}

		return new self(
			$isAnySelected,
			$isCampsSelected,
			$isUnitsSelected,
			$isEventsSelected,
			$isEventsWithParentsSelected,
		);
	}

}
