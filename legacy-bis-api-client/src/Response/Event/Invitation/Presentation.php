<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation;


final class Presentation
{

	/**
	 * @param Photo[] $photos
	 */
	private function __construct(
		private ?string $text,
		private array $photos = [],
	) {}


	/**
	 * @param Photo[] $photos
	 */
	public static function from(?string $text, array $photos): self
	{
		return new self($text, $photos);
	}


	public function hasText(): bool
	{
		return $this->text !== null;
	}


	public function getText(): ?string
	{
		return $this->text;
	}


	public function hasAnyPhotos(): bool
	{
		return \count($this->photos) > 0;
	}

	/**
	 * @return Photo[]
	 */
	public function getPhotos(): array
	{
		return $this->photos;
	}

}
