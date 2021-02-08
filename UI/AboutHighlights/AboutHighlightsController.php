<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\AboutHighlights;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class AboutHighlightsController implements Controller
{

	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}


	public function render(): void
	{
		$this->latte->render(
			__DIR__ . '/AboutHighlightsController.latte',
			\array_merge($this->base->getLayoutVariables('abouthighlights')),
		);
	}

}
