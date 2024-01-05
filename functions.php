<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;

use HnutiBrontosaurus\Theme\Events\Events;
use HnutiBrontosaurus\Theme\Rewrites\Event;
use HnutiBrontosaurus\Theme\Rewrites\Opportunity;
use WP_Theme;
use function __;
use function add_action;
use function add_filter;
use function add_theme_support;
use function flush_rewrite_rules;
use function register_nav_menus;
use function wp_get_theme;


/** @var Container $hb_container */
$hb_container = require_once __DIR__ . '/bootstrap.php';


(function (Container $container, WP_Theme $theme) {

	/**
	 * turn off <link rel="canonical"> produced by rank math seo plugin
	 * that's because on `/akce/ID` and `/prilezitost/ID` it generates bad URL (with no ID) and I couldn't find any simple way to modify it inside of controller
	 */
	add_filter('rank_math/frontend/canonical', static fn($canonical) => null);


	add_action('init', function () use ($container) {
		Event::rewriteRule();
		Opportunity::rewriteRule();

		PostTypeInitializer::novinky();
		PostTypeInitializer::pribehyNadseni();
		PostTypeInitializer::kontakty();

		register_nav_menus([
			'header' => __('Hlavička'),
			'footer-left' => __('Patička – vlevo'),
			'footer-center' => __('Patička – uprostřed'),
			'footer-right' => __('Patička – vpravo'),
		]);

		Events::init($container);
	});

	add_filter('query_vars', function($vars) {
		Event::queryVars($vars);
		Opportunity::queryVars($vars);
		return $vars;
	});

	add_action('after_switch_theme', function () {
		Event::rewriteRule();
		Opportunity::rewriteRule();
		flush_rewrite_rules();
	});

	add_action('after_setup_theme', function () {
		add_theme_support('post-thumbnails');
		add_theme_support('title-tag');
	});

	add_action('wp_enqueue_scripts', function () use ($theme) {
		Assets::script('expandable', $theme);
		Assets::script('lazyLoad', $theme);
		Assets::script('menuHandler', $theme);
		Assets::style('style', $theme);
	});

	add_action('enqueue_block_editor_assets', function () use ($theme) {
		Assets::script('editor', $theme);
		Assets::style('editor', $theme);
	});

})($hb_container, wp_get_theme());
