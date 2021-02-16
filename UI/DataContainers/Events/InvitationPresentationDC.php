<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\BisApiClient\Response\Event\Invitation\Photo;
use HnutiBrontosaurus\BisApiClient\Response\Event\Invitation\Presentation;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use HnutiBrontosaurus\Theme\UI\Utils;


/**
 * @property-read bool $hasText
 * @property-read string $text
 * @property-read bool $hasAnyPhotos
 * @property-read string[] $photos
 */
final class InvitationPresentationDC
{
	use PropertyHandler;


	/** @var bool */
	private $hasText = FALSE;

	/** @var string|null */
	private $text;

	/** @var bool */
	private $hasAnyPhotos = FALSE;

	/** @var string[] */
	private $photos;


	private function __construct($text = NULL, array $photos = [])
	{
		if ($text !== NULL) {
			$this->hasText = TRUE;
			$this->text = Utils::handleNonBreakingSpaces($text);
		}

		if (\count($photos) > 0) {
			$this->hasAnyPhotos = TRUE;
			$this->photos = \array_map(function (Photo $photo) {
				return $photo->getPath();
			}, $photos);
		}
	}

	public static function fromDTO(Presentation $presentation)
	{
		return new self(
			$presentation->hasText() ? $presentation->getText() : NULL,
			$presentation->hasAnyPhotos() ? $presentation->getPhotos() : []
		);
	}

}
