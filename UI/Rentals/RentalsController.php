<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Rentals;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;
use function add_action;
use function wp_enqueue_script;
use function wp_get_theme;


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

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-rentals-lightbox', $theme->get_template_directory_uri() . '/frontend/dist/js/lightbox.js', [], $themeVersion);
		});

		$this->latte->render(
			__DIR__ . '/RentalsController.latte',
			\array_merge($this->base->getLayoutVariables('rentals'), $params),
		);
	}

}
