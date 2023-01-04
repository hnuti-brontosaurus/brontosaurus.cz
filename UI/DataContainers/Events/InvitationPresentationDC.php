<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Response\Event\Photo;
use HnutiBrontosaurus\BisClient\Response\Event\Presentation;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use HnutiBrontosaurus\Theme\UI\Utils;


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


	public static function fromDTO(Presentation $presentation): self
	{
		$text = $presentation->getText();
		$photos = $presentation->getPhotos();
		return new self(
			$text !== null,
			$text !== null ? Utils::handleNonBreakingSpaces($text) : null,
			\count($photos) > 0,
			\array_map(fn(Photo $photo): string => $photo->getMediumSizePath(), $photos),
		);
	}

}
