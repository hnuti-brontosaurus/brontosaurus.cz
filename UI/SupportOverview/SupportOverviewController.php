<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\SupportOverview;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class SupportOverviewController implements Controller
{

	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}


	public function render(): void
	{
		$this->latte->render(
			__DIR__ . '/SupportOverviewController.latte',
			\array_merge($this->base->getLayoutVariables('supportoverview')),
		);
	}

}
