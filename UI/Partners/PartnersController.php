<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Partners;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class PartnersController implements Controller
{

	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}


	public function render(): void
	{
		$params = [
			'logosPath' => get_template_directory_uri() . '/UI/Partners/logos',
		];

		$this->latte->render(
			__DIR__ . '/PartnersController.latte',
			\array_merge($this->base->getLayoutVariables('partners'), $params),
		);
	}

}
