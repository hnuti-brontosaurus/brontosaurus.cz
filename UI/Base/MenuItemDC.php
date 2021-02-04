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

	public static function fromPost(\WP_Post $post): static
	{
		return new static(
			$post->title,
			$post->url,
			false, // todo; count with subpages as well
			false, // todo
		);
	}
}
