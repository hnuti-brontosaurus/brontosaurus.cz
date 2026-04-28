<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use function array_map;

final /* readonly */ class StoriesFiltersDC
{

	public bool $isAnySelected = false;
	/** stdClass<{label: string, slug: string, isSelected: bool}>[] */
	private array $filters = [];

	private function __construct(array $filters, ?string $selectedFilter = null)
	{
		$this->filters = $filters;
		$this->isAnySelected = $selectedFilter !== null;
	}

	public static function from(array $filters, ?string $selectedFilter = null): self
	{
		return new self(
			array_map(static fn ($filter) => (object) [
				'label' => mb_strtolower($filter->name),
				'slug' => $filter->slug,
				'isSelected' => $filter->slug === $selectedFilter,
			], $filters),
			$selectedFilter,
		);
	}

	public function get(): array
	{
		return $this->filters;
	}

}
