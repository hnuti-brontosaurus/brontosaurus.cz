<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Base;


final class Base
{
	public function __construct(
		private \WP_Post $currentPost,
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
			'enableTracking' => false, // todo
			'isOnSearchResultsPage' => $this->currentPost->post_name === 'vysledky-vyhledavani',
			'searchResultsPageLink' => $this->getLinkFor('vysledky-vyhledavani'),
			'isOnEnglishPage' => $this->currentPost->post_name === 'english',
			'homePageLink' => get_home_url(),
			'englishPageLink' => $this->getLinkFor('english'),
			'headerNavigationMenuItems' => $this->getMenuItemsFor('header'),
			'isOnFutureEventsPage' => $this->currentPost->post_name === 'co-se-chysta',
			'futureEventsPageLink' => $this->getLinkFor('co-se-chysta'),
			'pageClassSelector' => $pageClassSelector,
			'isOnPartnersPage' => $this->currentPost->post_name === 'nasi-partneri',
			'partnersPageLink' => $this->getLinkFor('nasi-partneri'),
			'footerNavigationMenuItems' => $this->getMenuItemsFor('footer'),

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
		return \array_map(
			fn(\WP_Post $post): MenuItemDC => MenuItemDC::fromPost($post, $this->currentPost),
			wp_get_nav_menu_items($menu)
		);
	}

	// todo: extract rather to LinkEvaluator service
	public function getLinkFor(string $slug): string
	{
		return get_permalink(get_page_by_path($slug)->ID);
	}

}
