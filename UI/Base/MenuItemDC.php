<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Base;

use HnutiBrontosaurus\Theme\UI\AboutCrossroad\AboutCrossroadController;
use HnutiBrontosaurus\Theme\UI\AboutHighlights\AboutHighlightsController;
use HnutiBrontosaurus\Theme\UI\AboutStructure\AboutStructureController;
use HnutiBrontosaurus\Theme\UI\AboutSuccesses\AboutSuccessesController;
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
		$menuItemSlug = \basename($menuItemPost->url); // source: https://wordpress.stackexchange.com/questions/249069/how-can-i-get-the-page-url-slug-when-post-name-returns-an-id
		return new static(
			$menuItemPost->title,
			$menuItemPost->url,
			$menuItemSlug === $currentPost?->post_name
			// about brontosaurus subpages hack
			|| (
				\in_array($currentPost->post_name, [AboutHighlightsController::PAGE_SLUG, AboutStructureController::PAGE_SLUG, AboutSuccessesController::PAGE_SLUG])
				&& $menuItemSlug === AboutCrossroadController::PAGE_SLUG
			),
			$menuItemPost->type === 'custom',
		);
	}
}
