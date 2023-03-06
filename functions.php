<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;

use Brick\DateTime\LocalDate;
use HnutiBrontosaurus\BisClient\Opportunity\Category;
use HnutiBrontosaurus\Theme\UI\Event\EventController;
use WP_Post;
use WP_Theme;
use function __;
use function add_action;
use function add_filter;
use function add_meta_box;
use function add_rewrite_rule;
use function add_theme_support;
use function array_key_exists;
use function array_push;
use function esc_attr;
use function filemtime;
use function flush_rewrite_rules;
use function get_permalink;
use function get_post_meta;
use function get_posts;
use function implode;
use function register_nav_menus;
use function register_post_type;
use function reset;
use function sprintf;
use function update_post_meta;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_get_theme;


require_once __DIR__ . '/vendor/autoload.php';


/**
 * turn off <link rel="canonical"> produced by rank math seo plugin
 * that's because on `/akce/ID` and `/prilezitost/ID` it generates bad URL (with no ID) and I couldn't find any simple way to modify it inside of controller
 */
add_filter('rank_math/frontend/canonical', static fn($canonical) => null);

// todo: possibly place elsewhere
function registerEvent(): void
{
	add_rewrite_rule(
		sprintf('^%s/([\d]+)',
			EventController::PAGE_SLUG,
		),
		sprintf('index.php?pagename=%s&%s=$matches[1]',
			EventController::PAGE_SLUG,
			EventController::PARAM_EVENT_ID,
		),
		'top',
	);
}
const HB_OPPORTUNITY_ID = 'opportunityId';
function registerOpportunityDetail(): void
{
	add_rewrite_rule(
		'^zapoj-se/prilezitost/([\d]+)',
		sprintf('index.php?pagename=prilezitost&%s=$matches[1]', HB_OPPORTUNITY_ID),
		'top',
	);
}
add_action('init', function () {
	registerEvent();
	registerOpportunityDetail();
});
add_filter('query_vars', function($vars) {
	array_push($vars, EventController::PARAM_EVENT_ID);
	array_push($vars, HB_OPPORTUNITY_ID);
	return $vars;
});
add_action('after_switch_theme', function () {
	registerEvent();
	registerOpportunityDetail();
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
		'show_in_rest' => true,
		'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
		'hierarchical' => false,
	]);

	register_post_type('pribehy-nadseni', [
		'labels' => [
			'name' => 'Příběhy nadšení',
			'singular_name' => 'Příběh nadšení',
			'add_new' => 'Přidat příběh',
			'add_new_item' => 'Přidat příběh',
		],
		'menu_icon' => 'dashicons-testimonial',
		'has_archive' => true,
		'public' => true,
		'show_ui' => true,
		'show_in_rest' => true,
		'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
		'hierarchical' => false,
	]);
	add_action('add_meta_boxes', function () {
		add_meta_box(
			'pribehy-nadseni_location',
			'Místo',
			function (WP_Post $post) {
				?>
				<div style="margin-bottom: 1em;">
					<input type="text" name="pribehy-nadseni_location" required><?php echo esc_attr(get_post_meta($post->ID, 'pribehy-nadseni_location', true)); ?></input>
				</div>
				<?php
			},
			'pribehy-nadseni',
			'side',
		);
	});
	add_action('save_post', function ($postId) {
		foreach (['pribehy-nadseni_location'] as $field) {
			if (array_key_exists($field, $_POST)) {
				update_post_meta(
					$postId,
					$field,
					$_POST[$field],
				);
			}
		}
	});

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
			function (WP_Post $post) {
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
				update_post_meta(
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
	$post = reset($posts);
	if ($post === false) {
		throw new \RuntimeException("$slug does not exist");
	}

	return get_permalink($post->ID);
}


function hb_dateSpan(LocalDate $start, LocalDate $end, string $dateFormat): string
{
	$start = $start->toNativeDateTimeImmutable();
	$end = $end->toNativeDateTimeImmutable();
	$dateSpan_untilPart = $end->format($dateFormat);

	$onlyOneDay = $start->format('j') === $end->format('j');
	if ($onlyOneDay) {
		return $dateSpan_untilPart;
	}

	$inSameMonth = $start->format('n') === $end->format('n');
	$inSameYear = $start->format('Y') === $end->format('Y');

	$dateSpan_fromPart = $start->format(\sprintf('j.%s%s',
		( ! $inSameMonth || ! $inSameYear) ? ' n.' : '',
		( ! $inSameYear) ? ' Y' : ''
	));

	// Czech language rules say that in case of multi-word date span there should be a space around the dash (@see http://prirucka.ujc.cas.cz/?id=810)
	$optionalSpace = '';
	if ( ! $inSameMonth) {
		$optionalSpace = ' ';
	}

	return $dateSpan_fromPart . \sprintf('%s–%s', $optionalSpace, $optionalSpace) . $dateSpan_untilPart;
}


function hb_opportunityCategoryToString(Category $category): string
{
	return match ($category) {
		Category::ORGANIZING() => 'organizování akcí',
		Category::COLLABORATION() => 'spolupráce',
		Category::LOCATION_HELP() => 'pomoc lokalitě',
	};
}


// frontend stuff

(function (WP_Theme $theme) {
	$path = static fn(string ...$parts): string => implode('/', $parts);
	add_action('wp_enqueue_scripts', function () use ($path, $theme) {
		$jsRelativePath = 'frontend/dist/js/menuHandler.js';
		wp_enqueue_script(
			handle: 'brontosaurus-menu-handler',
			src: $path($theme->get_template_directory_uri(), $jsRelativePath),
			ver: filemtime($path($theme->get_template_directory(), $jsRelativePath)),
		);
		$cssRelativePath = 'frontend/dist/css/style.css';
		wp_enqueue_style(
			handle: 'brontosaurus-main',
			src: $path($theme->get_template_directory_uri(), $cssRelativePath),
			ver: filemtime($path($theme->get_template_directory(), $cssRelativePath)),
		);
	});

	add_action('enqueue_block_editor_assets', function () use ($path, $theme) {
		$jsRelativePath = 'frontend/dist/js/editor.js';
		wp_enqueue_script(
			handle: 'brontosaurus-editor',
			src: $path($theme->get_template_directory_uri(), $jsRelativePath),
			ver: filemtime($path($theme->get_template_directory(), $jsRelativePath)),
		);
		$cssRelativePath = 'frontend/dist/css/editor.css';
		wp_enqueue_style(
			handle: 'brontosaurus-editor',
			src: $path($theme->get_template_directory_uri(), $cssRelativePath),
			ver: filemtime($path($theme->get_template_directory(), $cssRelativePath)),
		);
	});

	add_action('init', function () {
		register_nav_menus([
			'header' => __('Hlavička'),
			'footer-left' => __('Patička – vlevo'),
			'footer-center' => __('Patička – uprostřed'),
			'footer-right' => __('Patička – vpravo'),
		]);
	});
})(wp_get_theme());
