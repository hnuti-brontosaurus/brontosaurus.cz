<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\DataContainers;

use DateTimeImmutable;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use HnutiBrontosaurus\Theme\UI\Utils;
use function get_the_post_thumbnail_url;


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
final class NewsPost
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


	public static function from(\WP_Post $post): self
	{
		$thumbnail = get_the_post_thumbnail_url($post);
		$thumbnail = $thumbnail === false ? null : $thumbnail; // convert false to null which makes more sense

		return new self(
			$post->ID,
			Utils::handleNonBreakingSpaces($post->post_title),
			$post->post_name,
			DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $post->post_date),
			Utils::handleNonBreakingSpaces($post->post_excerpt),
			Utils::handleNonBreakingSpaces($post->post_content),
			$thumbnail !== null,
			$thumbnail,
		);
	}

}
