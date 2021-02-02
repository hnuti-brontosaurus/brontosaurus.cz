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
			'headerNavigationMenuItems' => [
				// todo to some DC
				(object) [
					'label' => '',
					'link' => '',
					'isActive' => false, // todo count with subpages as well
					'isExternalLink' => false,
				],
			],
			'isOnFutureEventsPage' => false,
			'futureEventsPageLink' => '',
			'pageClassSelector' => $pageClassSelector,
			'isOnPartnersPage' => false,
			'partnersPageLink' => '',
			'footerNavigationMenuItems' => [],
		];
	}

}
