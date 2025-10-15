<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use HnutiBrontosaurus\Theme\Filters\VoluntaryFilters;
use HnutiBrontosaurus\Theme\PropertyHandler;
use stdClass;
use function array_map;

final /* readonly */ class StoriesFiltersDC
{

	public bool $isAnySelected = false;
	/** stdClass<{label: string, slug: string, isSelected: bool}>[] */
	private array $filters = [];

	private function __construct(array $filters, string $key, ?string $selectedFilter = null)
	{
		$this->filters = $filters;
		$this->key = $key;
		$this->isAnySelected = $selectedFilter !== null;
	}

	public static function from(array $filters, string $key, ?string $selectedFilter = null): self
	{
		var_dump("aaaaa");
		return new self([], "");
		/*return new self(
			array_map(($filter) => [
				'label' => $filter->name,
				'slug' => $filter->slug,
				'isSelected' => $filter->slug === $selectedFilter,
			], $filters),
			$key, 
			$selectedFilter,
		);*/
	}

	public function get(): array
	{
		return $this->filters;
	}

}
