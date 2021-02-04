<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Base;


final class Base
{

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

			// todo
			'enableTracking' => false,
			'isOnSearchResultsPage' => false,
			'searchResultsPageLink' => '',
			'isOnEnglishPage' => false,
			'homePageLink' => '',
			'englishPageLink' => '',
			'headerNavigationMenuItems' => self::getMenuItemsFor('header'),
			'isOnFutureEventsPage' => false,
			'futureEventsPageLink' => '',
			'pageClassSelector' => $pageClassSelector,
			'isOnPartnersPage' => false,
			'partnersPageLink' => '',
			'footerNavigationMenuItems' => self::getMenuItemsFor('footer'),
		];
	}

	/**
	 * @param string $themeLocation
	 * @return MenuItemDC[]
	 */
	private static function getMenuItemsFor(string $themeLocation): array
	{
		$theme_locations = get_nav_menu_locations();
		$menu = get_term($theme_locations[$themeLocation], 'nav_menu');
		return \array_map(
			fn(\WP_Post $post): MenuItemDC => MenuItemDC::fromPost($post),
			wp_get_nav_menu_items($menu)
		);
	}

}
