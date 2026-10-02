<?php

namespace HnutiBrontosaurus\Theme\DataContainers;

use DateTimeImmutable;
use function get_the_post_thumbnail_url;


final readonly class NewsPost
{

	private function __construct(
		public int $id,
		public string $title,
		public string $slug,
		public DateTimeImmutable $date,
		public ?string $perex,
		public ?string $content,
		public bool $hasCoverImage,
		public ?string $coverImage,
	) {}


	public static function from(\WP_Post $post): self
	{
		$thumbnail = get_the_post_thumbnail_url($post);
		$thumbnail = $thumbnail === false ? null : $thumbnail; // convert false to null which makes more sense

		return new self(
			$post->ID,
			hb_handleNonBreakingSpaces($post->post_title),
			$post->post_name,
			DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $post->post_date),
			hb_handleNonBreakingSpaces($post->post_excerpt),
			hb_handleNonBreakingSpaces($post->post_content),
			$thumbnail !== null,
			$thumbnail,
		);
	}

}
