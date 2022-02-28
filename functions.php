<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;

use HnutiBrontosaurus\Theme\UI\Courses\CoursesController;
use HnutiBrontosaurus\Theme\UI\EventDetail\EventDetailController;
use HnutiBrontosaurus\Theme\UI\ForChildren\ForChildrenController;
use HnutiBrontosaurus\Theme\UI\Future\FutureController;
use HnutiBrontosaurus\Theme\UI\Meetups\MeetupsController;
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
add_action('init', function () {
	registerEventDetail();
});
add_filter('query_vars', function($vars) {
	array_push($vars, EventDetailController::PARAM_EVENT_ID);
	return $vars;
});
add_action('after_switch_theme', function () {
	registerEventDetail();
	flush_rewrite_rules();
});
add_action('after_setup_theme', function () {
	add_theme_support('post-thumbnails');
	add_theme_support('title-tag');
});
add_action('init', function () {
	register_post_type('novinky', [
		'labels' => [
			'name' => 'Novinky',
			'singular_name' => 'Novinka',
			'add_new' => 'Přidat novinku',
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

	register_post_type('contacts', [
		'labels' => [
			'name' => 'Kontakty',
			'singular_name' => 'Kontakt',
			'add_new' => 'Přidat kontakt',
			'add_new_item' => 'Přidat kontakt',
		],
		'menu_icon' => 'dashicons-phone',
		'has_archive' => true,
		'public' => true,
		'show_ui' => true,
		'show_in_rest' => false, // disables gutenberg (it is not ready for various styles)
		'supports' => ['title', 'thumbnail'],
		'hierarchical' => false,
	]);
	add_action('add_meta_boxes', function () {
		add_meta_box(
			'contacts_role',
			'Kontakt',
			function (\WP_Post $post) {
				?>
				<div style="margin-bottom: 1em;">
					<label for="contacts_role" class="required">Funkce</label><br>
					<textarea name="contacts_role" cols=60" rows="3" id="contacts_role"><?php echo \esc_attr(\get_post_meta($post->ID, 'contacts_role', true)); ?></textarea>
				</div>

				<div style="margin-bottom: 1em;">
					<label for="contacts_about" class="required">Povídání</label><br>
					<textarea name="contacts_about" cols="60" rows="5" id="contacts_about"><?php echo \esc_attr(\get_post_meta($post->ID, 'contacts_about', true)); ?></textarea>
				</div>

				<div style="margin-bottom: 1em;">
					<label for="contacts_email" class="required">E-mail</label><br>
					<textarea name="contacts_email" cols=60" rows="3" id="contacts_email"><?php echo \esc_attr(\get_post_meta($post->ID, 'contacts_email', true)); ?></textarea>
					<br>
					(jeden nebo více adres oddělených novým řádkem)
				</div>
				<?php
			},
			'contacts',
		);
	});
	add_action('save_post', function ($postId) {
		foreach (['contacts_role', 'contacts_about', 'contacts_email'] as $field) {
			if (array_key_exists($field, $_POST)) {
				\update_post_meta(
					$postId,
					$field,
					$_POST[$field],
				);
			}
		}
	});
});


function getLinkFor(string $slug): string
{
	$posts = get_posts(['name' => $slug, 'post_type' => 'page', 'posts_per_page' => 1]);
	$post = \reset($posts);
	if ($post === false) {
		throw new \RuntimeException("$slug does not exist");
	}

	return get_permalink($post->ID);
}


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
