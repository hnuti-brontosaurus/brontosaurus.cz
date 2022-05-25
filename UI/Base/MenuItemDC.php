<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Base;

use HnutiBrontosaurus\Theme\UI\AboutCrossroad\AboutCrossroadController;
use HnutiBrontosaurus\Theme\UI\AboutHighlights\AboutHighlightsController;
use HnutiBrontosaurus\Theme\UI\AboutStructure\AboutStructureController;
use HnutiBrontosaurus\Theme\UI\AboutSuccesses\AboutSuccessesController;
use HnutiBrontosaurus\Theme\UI\BaseUnitsAndClubsList\BaseUnitsAndClubsListController;
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
		return new static(
			$menuItemPost->title,
			$menuItemPost->url,
			self::isActive($menuItemPost, $currentPost),
			$menuItemPost->type === 'custom',
		);
	}

	private static function isActive(\WP_Post $menuItemPost, ?\WP_Post $currentPost): bool
	{
		$menuItemSlug = \basename($menuItemPost->url); // source: https://wordpress.stackexchange.com/questions/249069/how-can-i-get-the-page-url-slug-when-post-name-returns-an-id

		if ($currentPost === null) {
			return false;
		}

		if ($menuItemSlug === $currentPost->post_name) {
			return true;
		}

		// about brontosaurus subpages
		if ($menuItemSlug === AboutCrossroadController::PAGE_SLUG && \in_array($currentPost->post_name, [
			AboutHighlightsController::PAGE_SLUG,
				AboutStructureController::PAGE_SLUG,
				AboutSuccessesController::PAGE_SLUG,
				BaseUnitsAndClubsListController::PAGE_SLUG,
		])) {
			return true;
		}

		return false;
	}
}
