<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation\Photo;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation\Presentation;
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
		return new self(
			$presentation->hasText(),
			$presentation->hasText() ? Utils::handleNonBreakingSpaces($presentation->getText()) : null,
			$presentation->hasAnyPhotos(),
			$presentation->hasAnyPhotos() ? \array_map(static fn(Photo $photo) => $photo->getPath(), $presentation->getPhotos()) : [],
		);
	}

}
