<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\ForChildren;


use HnutiBrontosaurus\Theme\UI\PropertyHandler;


/**
 * @property-read string $key
 * @property-read bool $isAnySelected
 * @property-read bool $isCampsSelected
 * @property-read bool $isUnitsSelected
 * @property-read bool $isEventsSelected
 * @property-read bool $isEventsWithParentsSelected
 */
final class ForChildrenFiltersDC
{
	use PropertyHandler;

	private string $key;
	private bool $isAnySelected = false;
	private bool $isCampsSelected = false;
	private bool $isUnitsSelected = false;
	private bool $isEventsSelected = false;
	private bool $isEventsWithParentsSelected = false;


	private function __construct(string $key, ?string $selectedFilter = null)
	{
		$this->key = $key;

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


	public static function from(string $key, ?string $selectedFilter = null): self
	{
		return new self($key, $selectedFilter);
	}

}
