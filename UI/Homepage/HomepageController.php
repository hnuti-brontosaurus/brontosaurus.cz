<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Homepage;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class HomepageController implements Controller
{

	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}


	public function render(): void
	{
		$params = [
			'aboutCrossroadPageLink' => $this->base->getLinkFor('o-brontosaurovi'),
		];

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-homepage-references', $theme->get_template_directory_uri() . '/frontend/dist/js/references.js', [], $themeVersion);
		});

		$this->latte->render(
			__DIR__ . '/HomepageController.latte',
			\array_merge($this->base->getLayoutVariables('home'), $params),
		);
	}

}
