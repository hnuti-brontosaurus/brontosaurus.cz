<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\English;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class EnglishController implements Controller
{

	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}


	public function render(): void
	{
		$this->latte->render(
			__DIR__ . '/EnglishController.latte',
			\array_merge($this->base->getLayoutVariables('english')),
		);
	}

}
