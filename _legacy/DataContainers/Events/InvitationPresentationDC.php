<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Response\Image;


final class InvitationPresentationDC
{

	/**
	 * @param string[] $photos
	 */
	private function __construct(
		public readonly bool $hasText,
		public readonly ?string $text,
		public readonly bool $hasAnyPhotos,
		public readonly array $photos,
	) {}


	/**
	 * @param Image[] $photos
	 */
	public static function fromDTO(?string $text, array $photos): self
	{
		return new self(
			$text !== null,
			$text !== null ? hb_handleNonBreakingSpaces($text) : null,
			\count($photos) > 0,
			\array_map(fn(Image $photo): string => $photo->getMediumSizePath(), $photos),
		);
	}

}
