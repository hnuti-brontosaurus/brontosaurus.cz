<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;

use HnutiBrontosaurus\Theme\UI\Courses\CoursesController;
use HnutiBrontosaurus\Theme\UI\EventDetail\EventDetailController;
use HnutiBrontosaurus\Theme\UI\ForChildren\ForChildrenController;
use HnutiBrontosaurus\Theme\UI\Future\FutureController;
use HnutiBrontosaurus\Theme\UI\Meetups\MeetupsController;
use HnutiBrontosaurus\Theme\UI\News\NewsController;
use HnutiBrontosaurus\Theme\UI\Voluntary\VoluntaryController;


require_once __DIR__ . '/vendor/autoload.php';


// todo: possibly place elsewhere
function registerEventDetail(): void
{
	add_rewrite_rule(
		\sprintf('^(%s)/%s/([\d]+)',
			\implode('|', [
				VoluntaryController::PAGE_SLUG,
				CoursesController::PAGE_SLUG,
				MeetupsController::PAGE_SLUG,
				ForChildrenController::PAGE_SLUG,
				FutureController::PAGE_SLUG,
			]),
			EventDetailController::PAGE_SLUG,
		),
		\sprintf('index.php?pagename=%s&%s=$matches[1]&%s=$matches[2]',
			EventDetailController::PAGE_SLUG,
			EventDetailController::PARAM_PARENT_PAGE_SLUG,
			EventDetailController::PARAM_EVENT_ID,
		),
		'top',
	);
}
function registerNews(): void
{
	add_rewrite_rule(
		\sprintf('^%s/([\d]+)', NewsController::PAGE_SLUG),
		\sprintf('index.php?pagename=%s&%s=$matches[1]',
			NewsController::PAGE_SLUG,
			NewsController::PARAM_NEWS_ID,
		),
		'top',
	);
}
add_action('init', function () {
	registerEventDetail();
	registerNews();
});
add_filter('query_vars', function($vars) {
	array_push($vars, EventDetailController::PARAM_EVENT_ID);
	array_push($vars, NewsController::PARAM_NEWS_ID);
	return $vars;
});
add_action('after_switch_theme', function () {
	registerEventDetail();
	registerNews();
	flush_rewrite_rules();
});
add_action('after_setup_theme', function () {
	add_theme_support('post-thumbnails');
	add_theme_support('title-tag');
});
add_action('init', function () {
	register_post_type('news', [
		'labels' => [
			'name' => 'Novinky',
			'singular_name' => 'Novinka',
			'add_new' => 'Přidat',
			'add_new_item' => 'Přidat novinku',
		],
		'menu_icon' => 'dashicons-megaphone',
		'has_archive' => true,
		'public' => true,
		'show_ui' => true,
		'show_in_rest' => true, // disables gutenberg (it is not ready for various styles)
		'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
		'hierarchical' => false,
	]);
});


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
