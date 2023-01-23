<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Event\Response\Diet;
use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\BisClient\Event\Response\Food;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use HnutiBrontosaurus\Theme\UI\Utils;


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
		private string $accommodation,
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
			Utils::handleNonBreakingSpaces($event->getPropagation()->getInvitationTextIntroduction()),
			Utils::handleNonBreakingSpaces($event->getPropagation()->getInvitationTextPracticalInformation()),

			Utils::handleNonBreakingSpaces($accommodation),

			\count($food) > 0,
			\array_map(static fn(Diet $food): string => $foodLabels[$food->toScalar()], $food),

			$workDescription !== null,
			$workDescription !== null ? Utils::handleNonBreakingSpaces($workDescription) : null,
			$workDays !== null,
			$workDays,
			$workHoursPerDay !== null,
			$workHoursPerDay,
			$hasPresentation,
			$hasPresentation ? InvitationPresentationDC::fromDTO($text, $photos) : null,
		);
	}

}
