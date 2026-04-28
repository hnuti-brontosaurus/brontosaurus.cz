<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\Theme\Filters\CoursesFilters;


final class CoursesFiltersDC
{

	public readonly bool $isAnySelected;
	public readonly bool $isOrganizingSelected;
	public readonly bool $isThematicSelected;


	private function __construct(?string $selectedFilter = null)
	{
		$this->isAnySelected = false;
		$this->isOrganizingSelected = false;
		$this->isThematicSelected = false;

		if ($selectedFilter === null) {
			return;
		}

		$this->isAnySelected = true;

		switch ($selectedFilter) {
			case CoursesFilters::FILTER_ORGANIZING:
				$this->isOrganizingSelected = true;
				break;

			case CoursesFilters::FILTER_THEMATIC:
				$this->isThematicSelected = true;
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
