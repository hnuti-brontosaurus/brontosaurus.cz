<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation\Food;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation\Invitation;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation\Presentation;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use HnutiBrontosaurus\Theme\UI\Utils;


/**
 * @property-read string $introduction
 * @property-read string $organizationalInformation
 * @property-read bool $isAccommodationListed
 * @property-read string|null $accommodation
 * @property-read bool $isFoodListed
 * @property-read bool $isFoodOfTypeChooseable
 * @property-read bool $isFoodOfTypeVegetarian
 * @property-read bool $isFoodOfTypeNonVegetarian
 * @property-read string|null $workDescription
 * @property-read bool $areWorkHoursPerDayListed
 * @property-read int|null $workHoursPerDay
 * @property-read bool $hasPresentation
 * @property-read InvitationPresentationDC|null $presentation
 */
final class InvitationDC
{
	use PropertyHandler;

	private bool $isAccommodationListed;
	private ?string $accommodation;
	private bool $isFoodListed;
	private bool $isFoodOfTypeChooseable;
	private bool $isFoodOfTypeVegetarian;
	private bool $isFoodOfTypeNonVegetarian;
	private bool $areWorkHoursPerDayListed;
	private ?int $workHoursPerDay;
	private bool $hasPresentation;
	private ?InvitationPresentationDC $presentation;


	private function __construct(
		private string $introduction,
		private string $organizationalInformation,
		?string $accommodation,
		Food $food,
		private ?string $workDescription,
		?int $workHoursPerDay,
		?Presentation $presentation,
	) {
		$this->isAccommodationListed = $accommodation !== null;
		$this->accommodation = $accommodation !== null ? Utils::handleNonBreakingSpaces($accommodation) : null;

		$this->isFoodListed = ! $food->equals(Food::NOT_LISTED());
		$this->isFoodOfTypeChooseable = $food->equals(Food::CHOOSEABLE());
		$this->isFoodOfTypeVegetarian = $food->equals(Food::VEGETARIAN());
		$this->isFoodOfTypeNonVegetarian = $food->equals(Food::NON_VEGETARIAN());

		$this->areWorkHoursPerDayListed = $workHoursPerDay !== null;
		$this->workHoursPerDay = $workHoursPerDay;

		$this->hasPresentation = $presentation !== null;
		$this->presentation = $presentation !== null ? InvitationPresentationDC::fromDTO($presentation) : null;
	}


	public static function fromDTO(Invitation $invitation): self
	{
		return new self(
			Utils::handleNonBreakingSpaces($invitation->getIntroduction()),
			Utils::handleNonBreakingSpaces($invitation->getOrganizationalInformation()),
			$invitation->getAccommodation(),
			$invitation->getFood(),
			$invitation->getWorkDescription() !== null ? Utils::handleNonBreakingSpaces($invitation->getWorkDescription()) : null,
			$invitation->getWorkHoursPerDay(),
			$invitation->getPresentation(),
		);
	}

}
