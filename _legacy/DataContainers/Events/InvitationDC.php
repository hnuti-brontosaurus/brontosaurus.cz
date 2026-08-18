<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Event\Response\Diet;
use HnutiBrontosaurus\BisClient\Event\Response\Event;


final class InvitationDC
{

	/**
	 * @param string[] $food
	 */
	private function __construct(
		public readonly string $introduction,
		public readonly string $organizationalInformation,
		public readonly bool $isAccommodationListed,
		public readonly ?string $accommodation,
		public readonly bool $isFoodListed,
		public readonly array $food,
		public readonly bool $isWorkDescriptionListed,
		public readonly ?string $workDescription,
		public readonly bool $areWorkDaysListed,
		public readonly ?int $workDays,
		public readonly bool $areWorkHoursPerDayListed,
		public readonly ?int $workHoursPerDay,
		public readonly bool $hasPresentation,
		public readonly ?InvitationPresentationDC $presentation,
	) {}


	public static function fromDTO(Event $event): self
	{
		$accommodation = $event->getPropagation()->getAccommodation();
		$food = $event->getPropagation()->getDiets();
		$workDescription = $event->getPropagation()->getInvitationTextWorkDescription();
		$workDays = $event->getPropagation()->getWorkingDays();
		$workHoursPerDay = $event->getPropagation()->getWorkingHours();

		$foodLabels = [
			Diet::MEAT->value => 'ne-vegetariánská',
			Diet::VEGETARIAN->value => 'vegetariánská',
			Diet::VEGAN->value => 'veganská',
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
			\array_map(static fn(Diet $food): string => $foodLabels[$food->value], $food),

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
