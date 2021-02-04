<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;


// frontend stuff

(function (\WP_Theme $theme) {
	add_action('wp_enqueue_scripts', function () use ($theme) {
		$themeVersion = $theme->get('Version');
		wp_enqueue_script('brontosaurus-menu-handler', $theme->get_template_directory_uri() . '/frontend/dist/js/menuHandler.js', [], $themeVersion);
		wp_enqueue_style('brontosaurus-main', $theme->get_template_directory_uri() . '/frontend/dist/css/style.css', [], $themeVersion);
	});

	add_action('init', function () {
		register_nav_menus([
			'header' => __('Hlavička'),
			'footer' => __('Patička'),
		]);
	});
})(wp_get_theme());
