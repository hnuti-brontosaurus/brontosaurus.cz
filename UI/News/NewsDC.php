<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\News;

use DateTimeImmutable;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use HnutiBrontosaurus\Theme\UI\Utils;


/**
 * @property-read int $id
 * @property-read string $title
 * @property-read string $slug
 * @property-read DateTimeImmutable $date
 * @property-read string|null $perex
 * @property-read string|null $content
 * @property-read bool $hasCoverImage
 * @property-read string|null $coverImage
 */
final class NewsDC
{
	use PropertyHandler;

	private function __construct(
		private int $id,
		private string $title,
		private string $slug,
		private DateTimeImmutable $date,
		private ?string $perex,
		private ?string $content,
		private bool $hasCoverImage,
		private ?string $coverImage,
	) {}


	public static function fromPost(\WP_Post $post): self
	{
		$thumbnail = get_the_post_thumbnail_url($post);
		return new self(
			$post->ID,
			Utils::handleNonBreakingSpaces($post->post_title),
			$post->post_name,
			DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $post->post_date),
			Utils::handleNonBreakingSpaces($post->post_excerpt),
			Utils::handleNonBreakingSpaces($post->post_content),
			$thumbnail !== '',
			$thumbnail !== '' ? $thumbnail : null,
		);
	}

}
