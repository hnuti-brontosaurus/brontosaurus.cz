<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\AboutCrossroad;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class AboutCrossroadController implements Controller
{
	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}

	public function render(): void
	{
		$params = [
			'aboutHighlightsPageLink' => $this->base->getLinkFor('jak-to-u-nas-funguje'),
			'aboutStructurePageLink' => $this->base->getLinkFor('struktura-organizace'),
			'aboutSuccessesPageLink' => $this->base->getLinkFor('nase-uspechy'),
		];

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-aboutCrossroad-references', $theme->get_template_directory_uri() . '/frontend/dist/js/references.js', [], $themeVersion);
		});

		$this->latte->render(
			__DIR__ . '/AboutCrossroadController.latte',
			\array_merge($this->base->getLayoutVariables('aboutcrossroad'), $params),
		);
	}

}
