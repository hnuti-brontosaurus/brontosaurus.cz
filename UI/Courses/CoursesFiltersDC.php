<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Courses;

use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $key
 * @property-read bool $isAnySelected
 * @property-read bool $isMeetupsSelected
 * @property-read bool $isOrganizingSelected
 * @property-read bool $isThematicSelected
 */
final class CoursesFiltersDC
{
	use PropertyHandler;

	private string $key;
	private bool $isAnySelected = false;
	private bool $isMeetupsSelected = false;
	private bool $isOrganizingSelected = false;
	private bool $isThematicSelected = false;


	private function __construct(string $key, ?string $selectedFilter = null)
	{
		$this->key = $key;

		if ($selectedFilter === null) {
			return;
		}

		$this->isAnySelected = true;

		switch ($selectedFilter) {
			case CoursesFilters::FILTER_TALKS:
				$this->isMeetupsSelected = true;
				break;

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


	public static function from(string $key, ?string $selectedFilter = null): self
	{
		return new self($key, $selectedFilter);
	}

}
