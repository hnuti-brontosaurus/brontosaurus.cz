<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Error;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class ErrorController implements Controller
{
	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}

	public function render(): void
	{
		$this->latte->render(
			__DIR__ . '/ErrorController.404.latte',
			$this->base->getLayoutVariables('error404'),
		);
	}

}
