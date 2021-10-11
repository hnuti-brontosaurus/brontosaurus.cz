<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Guidepost;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class GuidepostController implements Controller
{
	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}

	public function render(): void
	{
		$params = [
			'viewAssetsPath' => get_template_directory_uri() . '/UI/Guidepost/assets'
		];

		$this->latte->render(
			__DIR__ . '/GuidepostController.latte',
			\array_merge($this->base->getLayoutVariables('guidepost'), $params),
		);
	}

}
