<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\BisApiClient\Response\Event\Invitation\Food;
use HnutiBrontosaurus\BisApiClient\Response\Event\Invitation\Invitation;
use HnutiBrontosaurus\BisApiClient\Response\Event\Invitation\Presentation;
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


	/** @var string */
	private $introduction;

	/** @var string */
	private $organizationalInformation;

	/** @var bool */
	private $isAccommodationListed = false;

	/** @var string|null */
	private $accommodation;

	/** @var bool */
	private $isFoodListed = false;

	/** @var bool */
	private $isFoodOfTypeChooseable = false;

	/** @var bool */
	private $isFoodOfTypeVegetarian = false;

	/** @var bool */
	private $isFoodOfTypeNonVegetarian = false;

	/** @var string|null */
	private $workDescription;

	/** @var bool */
	private $areWorkHoursPerDayListed = false;

	/** @var int|null */
	private $workHoursPerDay;

	/** @var bool */
	private $hasPresentation = FALSE;

	/** @var InvitationPresentationDC|null */
	private $presentation;


	private function __construct(
		$introduction,
		$organizationalInformation,
		$accommodation,
		Food $food,
		$workDescription,
		$workHoursPerDay,
		Presentation $presentation = NULL
	) {
		$this->introduction = Utils::handleNonBreakingSpaces($introduction);
		$this->organizationalInformation = Utils::handleNonBreakingSpaces($organizationalInformation);

		if ($accommodation !== null) {
			$this->isAccommodationListed = true;
			$this->accommodation = Utils::handleNonBreakingSpaces($accommodation);
		}

		$this->isFoodListed = $food->isListed();
		$this->isFoodOfTypeChooseable = $food->isOfTypeChooseable();
		$this->isFoodOfTypeVegetarian = $food->isOfTypeVegetarian();
		$this->isFoodOfTypeNonVegetarian = $food->isOfTypeNonVegetarian();

		$this->workDescription = Utils::handleNonBreakingSpaces($workDescription);

		if ($workHoursPerDay !== null) {
			$this->areWorkHoursPerDayListed = true;
			$this->workHoursPerDay = $workHoursPerDay;
		}

		if ($presentation !== NULL) {
			$this->hasPresentation = TRUE;
			$this->presentation = InvitationPresentationDC::fromDTO($presentation);
		}
	}

	public static function fromDTO(Invitation $invitation)
	{
		return new self(
			$invitation->getIntroduction(),
			$invitation->getOrganizationalInformation(),
			$invitation->getAccommodation(),
			$invitation->getFood(),
			$invitation->getWorkDescription(),
			$invitation->getWorkHoursPerDay(),
			$invitation->getPresentation()
		);
	}

}
