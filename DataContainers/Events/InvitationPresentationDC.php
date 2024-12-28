<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Event\Response\Image;
use HnutiBrontosaurus\Theme\PropertyHandler;


/**
 * @property-read bool $hasText
 * @property-read string|null $text
 * @property-read bool $hasAnyPhotos
 * @property-read string[] $photos
 */
final class InvitationPresentationDC
{
	use PropertyHandler;

	/**
	 * @param string[] $photos
	 */
	private function __construct(
		private bool $hasText,
		private ?string $text,
		private bool $hasAnyPhotos,
		private array $photos,
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
			\array_map(fn(Image $photo): string => $photo->getMedium(), $photos),
		);
	}

}
