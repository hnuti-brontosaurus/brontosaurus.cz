<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\AboutSuccesses;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class AboutSuccessesController implements Controller
{
	public const PAGE_SLUG = 'nase-uspechy';

	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}


	public function render(): void
	{
		$this->latte->render(
			__DIR__ . '/AboutSuccessesController.latte',
			\array_merge($this->base->getLayoutVariables('aboutsuccesses')),
		);
	}

}
