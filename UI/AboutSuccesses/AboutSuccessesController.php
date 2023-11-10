<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\AboutSuccesses;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Contacts\ContactDC;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;
use WP_Post;
use function array_key_exists;
use function array_map;
use function count;
use function get_page_link;
use function get_post_custom;
use function get_post_meta;
use function get_posts;
use function get_the_post_thumbnail_url;
use function wp_enqueue_script;
use function wp_get_theme;


final class AboutSuccessesController implements Controller
{
	public const PAGE_SLUG = 'nase-uspechy';

	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}


	public function render(): void
	{
		$posts = get_posts(['post_type' => 'pribehy-nadseni', 'numberposts' => -1]);

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-aboutsuccesses-lazy-load', $theme->get_template_directory_uri() . '/frontend/dist/js/lazyLoad.js', [], $themeVersion);
		});

		$this->latte->render(
			__DIR__ . '/AboutSuccessesController.latte',
			\array_merge($this->base->getLayoutVariables('aboutsuccesses'), [
				'stories' => array_map(function (WP_Post $post) {
					$thumbnail = get_the_post_thumbnail_url($post);
					$thumbnail = $thumbnail === false ? null : $thumbnail; // convert false to null which makes more sense

					$customs = get_post_custom($post->ID);
					$location = count($customs) > 0 && array_key_exists('pribehy-nadseni_location', $customs) && array_key_exists(0, $customs['pribehy-nadseni_location'])
						? $customs['pribehy-nadseni_location'][0]
						: null;

					return (object) [
						'title' => $post->post_title,
						'location' => $location,
						'excerpt' => $post->post_excerpt,
						'hasThumbnail' => $thumbnail !== null,
						'thumbnail' => $thumbnail,
						'link' => get_page_link($post),
					];
				}, $posts),
				'supportOverviewLink' => $this->base->getLinkFor('podpor-nas'),
			]),
		);
	}

}
