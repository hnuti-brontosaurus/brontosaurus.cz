<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Base;

use HnutiBrontosaurus\Theme\UI\PropertyHandler;


final class MenuItemDC
{
	use PropertyHandler;

	public function __construct(
		private string $label,
		private string $link,
		private bool $isActive,
		private bool $isExternalLink,
	) {}

	public static function fromPost(
		\WP_Post $menuItemPost,
		?\WP_Post $currentPost,
	): static
	{
		// for some reason $menuItemPost->post_name does not return slug but ID todo fix it
//		dump($currentPost->post_name . ',' . $menuItemPost->post_name);
//		dump($menuItemPost);
//		dump(get_page_template_slug($menuItemPost));
		return new static(
			$menuItemPost->title,
			$menuItemPost->url,
			$menuItemPost->post_name === $currentPost?->post_name, // todo count with subpages as well
			false, // todo
		);
	}
}
