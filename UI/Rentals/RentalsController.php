<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Rentals;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class RentalsController implements Controller
{
	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}

	public function render(): void
	{
		$params = [
			'imagesPath' => get_template_directory_uri() . '/UI/Rentals/assets/dist/images',
		];

		$this->latte->render(
			__DIR__ . '/RentalsController.latte',
			\array_merge($this->base->getLayoutVariables('rentals'), $params),
		);
	}

}
