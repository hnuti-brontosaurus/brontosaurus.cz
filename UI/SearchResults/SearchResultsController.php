<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\SearchResults;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class SearchResultsController implements Controller
{
	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}

	public function render(): void
	{
		$params = [];

		$this->latte->render(
			__DIR__ . '/SearchResultsController.latte',
			\array_merge($this->base->getLayoutVariables('searchresults'), $params),
		);
	}

}
