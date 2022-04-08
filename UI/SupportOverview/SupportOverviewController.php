<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\SupportOverview;

use HnutiBrontosaurus\Theme\UI\AboutSuccesses\AboutSuccessesController;
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
		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-supportOverview-references', $theme->get_template_directory_uri() . '/frontend/dist/js/references.js', [], $themeVersion);
		});

		$params = [
			'aboutSuccessesLink' => $this->base->getLinkFor(AboutSuccessesController::PAGE_SLUG),
		];

		$this->latte->render(
			__DIR__ . '/SupportOverviewController.latte',
			\array_merge($this->base->getLayoutVariables('supportoverview'), $params),
		);
	}

}
