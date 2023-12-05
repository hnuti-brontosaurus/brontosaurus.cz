<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Base;


use function str_replace;
use const ABSPATH;


final class Base
{
	public function __construct(
		private bool $enableTracking,
		private ?\WP_Post $currentPost,
	) {}

	public function getLayoutPath(): string
	{
		return __DIR__ . '/Base.latte';
	}

	public function getLayoutVariables(string $pageClassSelector): array
	{
		return [
			'layoutPath' => $this->getLayoutPath(),
			'languageCode' => get_language_attributes(),
			'themePath' => get_template_directory_uri(),
			'themePathRelative' => str_replace(ABSPATH, '', get_template_directory_uri()),
			'enableTracking' => $this->enableTracking,
			'isOnSearchResultsPage' => $this->currentPost?->post_name === 'vysledky-vyhledavani',
			'searchResultsPageLink' => $this->getLinkFor('vysledky-vyhledavani'),
			'isOnEnglishPage' => $this->currentPost?->post_name === 'english',
			'homePageLink' => get_home_url(),
			'englishPageLink' => $this->getLinkFor('english'),
			'headerNavigationMenuItems' => $this->getMenuItemsFor('header'),
			'isOnFutureEventsPage' => $this->currentPost?->post_name === 'co-se-chysta',
			'futureEventsPageLink' => $this->getLinkFor('co-se-chysta'),
			'pageClassSelector' => $pageClassSelector,
			'isOnPartnersPage' => $this->currentPost?->post_name === 'nasi-partneri',
			'partnersPageLink' => $this->getLinkFor('nasi-partneri'),
			'footerLeftNavigationMenuItems' => $this->getMenuItemsFor('footer-left'),
			'footerCenterNavigationMenuItems' => $this->getMenuItemsFor('footer-center'),
			'footerRightNavigationMenuItems' => $this->getMenuItemsFor('footer-right'),

			'coursesPageLink' => $this->getLinkFor('kurzy-a-prednasky'),
			'voluntaryPageLink' => $this->getLinkFor('dobrovolnicke-akce'),
			'meetupsPageLink' => $this->getLinkFor('setkavani-a-kluby'),
			'supportOverviewPageLink' => $this->getLinkFor('podpor-nas'),
			'forChildrenPageLink' => $this->getLinkFor('pro-deti'),
		];
	}

	/**
	 * @param string $themeLocation
	 * @return MenuItemDC[]
	 */
	private function getMenuItemsFor(string $themeLocation): array
	{
		$theme_locations = get_nav_menu_locations();
		$menu = get_term($theme_locations[$themeLocation], 'nav_menu');
		$items = wp_get_nav_menu_items($menu);
		if ($items === false) {
			return [];
		}

		return \array_map(
			fn(\WP_Post $post): MenuItemDC => MenuItemDC::fromPost($post, $this->currentPost),
			$items
		);
	}

	// todo: extract rather to LinkEvaluator service
	public function getLinkFor(string $slug): string
	{
		$posts = get_posts(['name' => $slug, 'post_type' => 'page', 'posts_per_page' => 1]);
		$post = \reset($posts);
		if ($post === false) {
			throw new \RuntimeException("$slug does not exist");
		}

		return get_permalink($post->ID);
	}

}
