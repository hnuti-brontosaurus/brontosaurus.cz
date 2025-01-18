<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Event\Response\Diet;
use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\BisClient\Event\Response\Food;
use HnutiBrontosaurus\Theme\PropertyHandler;


/**
 * @property-read string $introduction
 * @property-read string $organizationalInformation
 * @property-read bool $isAccommodationListed
 * @property-read string|null $accommodation
 * @property-read bool $isFoodListed
 * @property-read string[] $food
 * @property-read bool $isWorkDescriptionListed
 * @property-read string|null $workDescription
 * @property-read bool $areWorkHoursPerDayListed
 * @property-read int|null $workHoursPerDay
 * @property-read bool $areWorkDaysListed
 * @property-read int|null $workDays
 * @property-read bool $hasPresentation
 * @property-read InvitationPresentationDC|null $presentation
 */
final class InvitationDC
{
	use PropertyHandler;


	/**
	 * @param string[] $food
	 */
	private function __construct(
		private string $introduction,
		private string $organizationalInformation,
		private bool $isAccommodationListed,
		private ?string $accommodation,
		private bool $isFoodListed,
		private array $food,
		private bool $isWorkDescriptionListed,
		private ?string $workDescription,
		private bool $areWorkDaysListed,
		private ?int $workDays,
		private bool $areWorkHoursPerDayListed,
		private ?int $workHoursPerDay,
		private bool $hasPresentation,
		private ?InvitationPresentationDC $presentation,
	) {}


	public static function fromDTO(Event $event): self
	{
		$accommodation = $event->getPropagation()->getAccommodation();
		$food = $event->getPropagation()->getDiets();
		$workDescription = $event->getPropagation()->getInvitationTextWorkDescription();
		$workDays = $event->getPropagation()->getWorkingDays();
		$workHoursPerDay = $event->getPropagation()->getWorkingHours();

		$foodLabels = [
			Diet::MEAT()->toScalar() => 'ne-vegetariánská',
			Diet::VEGETARIAN()->toScalar() => 'vegetariánská',
			Diet::VEGAN()->toScalar() => 'veganská',
		];

		$text = $event->getPropagation()->getInvitationTextAboutUs();
		$photos = $event->getPropagation()->getImages();
		$hasPresentation = $text !== null || \count($photos) > 0;

		return new self(
			hb_handleNonBreakingSpaces($event->getPropagation()->getInvitationTextIntroduction()),
			hb_handleNonBreakingSpaces($event->getPropagation()->getInvitationTextPracticalInformation()),

			$accommodation !== null,
			$accommodation !== null ? hb_handleNonBreakingSpaces($accommodation) : null,

			\count($food) > 0,
			\array_map(static fn(Diet $food): string => $foodLabels[$food->toScalar()], $food),

			$workDescription !== null,
			$workDescription !== null ? hb_handleNonBreakingSpaces($workDescription) : null,
			$workDays !== null,
			$workDays,
			$workHoursPerDay !== null,
			$workHoursPerDay,
			$hasPresentation,
			$hasPresentation ? InvitationPresentationDC::fromDTO($text, $photos) : null,
		);
	}

}
