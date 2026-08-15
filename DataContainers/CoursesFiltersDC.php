<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\Theme\Filters\CoursesFilters;


final class CoursesFiltersDC
{

	private function __construct(
		public readonly bool $isAnySelected,
		public readonly bool $isEducationalSelected,
		public readonly bool $isExperientalSelected,
		public readonly bool $isSingledaySelected,
		public readonly bool $isMultidaySelected,
	) {}

	public static function from(?string $selectedFilter = null): self
	{
		$isAnySelected = false;
		$isEducationalSelected = false;
		$isExperientalSelected = false;
		$isSingledaySelected = false;
		$isMultidaySelected = false;

		if ($selectedFilter === null) {
			return new self(
				$isAnySelected,
				$isEducationalSelected,
				$isExperientalSelected,
				$isSingledaySelected,
				$isMultidaySelected,
			);
		}

		$isAnySelected = true;

		switch ($selectedFilter) {
			case CoursesFilters::Educational:
				$isEducationalSelected = true;
				break;

			case CoursesFilters::Experiental:
				$isExperientalSelected = true;
				break;

			case CoursesFilters::Singleday:
				$isSingledaySelected = true;
				break;

			case CoursesFilters::Multiday:
				$isMultidaySelected = true;
				break;

			default:
				$isAnySelected = false;
				break;
		}

		return new self(
			$isAnySelected,
			$isEducationalSelected,
			$isExperientalSelected,
			$isSingledaySelected,
			$isMultidaySelected,
		);
	}

}
