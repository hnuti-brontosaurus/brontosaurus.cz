<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\Theme\Filters\CoursesFilters;


final class CoursesFiltersDC
{

	private function __construct(
		public readonly bool $isAnySelected,
		public readonly bool $isOrganizingSelected,
		public readonly bool $isThematicSelected,
	) {}

	public static function from(?string $selectedFilter = null): self
	{
		$isAnySelected = false;
		$isOrganizingSelected = false;
		$isThematicSelected = false;

		if ($selectedFilter === null) {
			return new self(
				$isAnySelected,
				$isOrganizingSelected,
				$isThematicSelected,
			);
		}

		$isAnySelected = true;

		switch ($selectedFilter) {
			case CoursesFilters::FILTER_ORGANIZING:
				$isOrganizingSelected = true;
				break;

			case CoursesFilters::FILTER_THEMATIC:
				$isThematicSelected = true;
				break;

			default:
				$isAnySelected = false;
				break;
		}

		return new self(
			$isAnySelected,
			$isOrganizingSelected,
			$isThematicSelected,
		);
	}

}
