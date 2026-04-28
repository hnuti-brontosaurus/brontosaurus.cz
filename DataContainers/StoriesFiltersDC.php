<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use function array_map;

final class StoriesFiltersDC
{

	public readonly bool $isAnySelected;
	/** stdClass<{label: string, slug: string, isSelected: bool}>[] */
	private readonly array $filters;

	private function __construct(array $filters, ?string $selectedFilter = null)
	{
		$this->isAnySelected = $selectedFilter !== null;
		$this->filters = $filters;
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

	/* helper method for better reading so that it's not $filters->filters */
	public function get(): array
	{
		return $this->filters;
	}

}
