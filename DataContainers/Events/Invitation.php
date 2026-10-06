<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Event\Response\Diet;
use HnutiBrontosaurus\BisClient\Event\Response\Event;


final readonly class Invitation
{

	/**
	 * @param string[] $food
	 */
	private function __construct(
		public string $introduction,
		public string $organizationalInformation,
		public bool $isAccommodationListed,
		public ?string $accommodation,
		public bool $isFoodListed,
		public array $food,
		public bool $isWorkDescriptionListed,
		public ?string $workDescription,
		public bool $areWorkDaysListed,
		public ?int $workDays,
		public bool $areWorkHoursPerDayListed,
		public ?int $workHoursPerDay,
		public bool $hasPresentation,
		public ?InvitationPresentation $presentation,
	) {}


	public static function fromDTO(Event $event): self
	{
		$accommodation = $event->propagation->accommodation;
		$food = $event->propagation->diets;
		$workDescription = $event->propagation->invitationTextWorkDescription;
		$workDays = $event->propagation->workingDays;
		$workHoursPerDay = $event->propagation->workingHours;

		$foodLabels = [
			Diet::MEAT->value => 'ne-vegetariánská',
			Diet::VEGETARIAN->value => 'vegetariánská',
			Diet::VEGAN->value => 'veganská',
		];

		$text = $event->propagation->invitationTextAboutUs;
		$photos = $event->propagation->images;
		$hasPresentation = $text !== null || \count($photos) > 0;

		return new self(
			hb_handleNonBreakingSpaces($event->propagation->invitationTextIntroduction),
			hb_handleNonBreakingSpaces($event->propagation->invitationTextPracticalInformation),

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
			$hasPresentation ? InvitationPresentation::fromDTO($text, $photos) : null,
		);
	}

}
