<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\BisClient\Enums\IntendedFor;
use HnutiBrontosaurus\BisClient\Response\Event\Event;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use HnutiBrontosaurus\Theme\UI\Utils;


/**
 * @property-read int $id
 * @property-read string $title
 * @property-read string $dateStartForHumans
 * @property-read string $dateStartForRobots
 * @property-read bool $hasTimeStart
 * @property-read string|null $timeStart
 * @property-read \DateTimeImmutable $dateEnd
 * @property-read string $dateSpan
 * @property-read int $duration
 * @property-read bool $isLongTime
 * @property-read PlaceDC $place
 * @property-read AgeDC $age
 * @property-read bool $isPaid
 * @property-read string|null $price
 * @property-read ContactDC $contact
 * @property-read bool $isRegistrationRequired
 * @property-read bool $isFull
 * @property-read bool $isForFirstTimeAttendees
 * @property-read InvitationDC $invitation
 * @property-read bool $areOrganizersListed
 * @property-read string|null $organizers
 * @property-read string|NULL $organizerUnit
 * @property-read bool $hasCoverPhoto
 * @property-read string|null $coverPhotoPath
 * @property-read bool $hasProgram
 * @property-read ProgramDC $program
 * @property-read bool $hasRelatedWebsite
 * @property-read string|null $relatedWebsite
 */
final class EventDC
{
	use PropertyHandler;


	private int $id;
	private string $title;
	private bool $hasCoverPhoto;
	private ?string $coverPhotoPath;
	private string $dateStartForHumans;
	private string $dateStartForRobots;
	private bool $hasTimeStart;
	private ?string $timeStart;
	private \DateTimeImmutable $dateEnd;
	private string $dateSpan;
	private int $duration;
	private bool $isLongTime;
	private PlaceDC $place;
	private AgeDC $age;
	private bool $isPaid;
	private ?string $price;
	private ContactDC $contact;
	private bool $isRegistrationRequired;
	private bool $isFull;
	private bool $isForFirstTimeAttendees;
	private InvitationDC $invitation;
	private bool $areOrganizersListed;
	private ?string $organizers;
	private ?string $organizerUnit;
	private ProgramDC $program;
	private bool $hasRelatedWebsite;
	private ?string $relatedWebsite;


	public function __construct(Event $event, string $dateFormatHuman, string $dateFormatRobot)
	{
		$this->id = $event->getId();
		$this->title = Utils::handleNonBreakingSpaces($event->getName());

		$coverPhotoPath = $event->getCoverPhotoPath();
		$this->hasCoverPhoto = $coverPhotoPath !== null;
		$this->coverPhotoPath = $event->getCoverPhotoPath()->getMediumSizePath(); // todo small?

		$this->dateStartForHumans = $event->getDateFrom()->format($dateFormatHuman);
		$this->dateStartForRobots = $event->getDateFrom()->format($dateFormatRobot);
		$time = $event->getDateFrom()->format('H:i');
		$hasTimeStart = $time !== '00:00'; // assuming that no event will start at midnight
		$this->hasTimeStart = $hasTimeStart;
		$this->timeStart = $hasTimeStart ? $time : null;

		$this->dateEnd = $event->getDateUntil();
		$this->dateSpan = $this->getDateSpan($event->getDateFrom(), $event->getDateUntil(), $dateFormatHuman);
		$this->place = PlaceDC::fromDTO($event->getLocation());
		$this->age = AgeDC::fromDTO($event);

		$price = $event->getPrice();
		$this->isPaid = $price !== null;
		$this->price = $price;

		$this->contact = ContactDC::fromDTO($event->getContactPerson());

		$this->isRegistrationRequired = $event->getIsRegistrationRequired();
		$this->isFull = $event->getIsFull();

		$this->isForFirstTimeAttendees = $event->getTargetGroup()->equals(IntendedFor::FIRST_TIME_PARTICIPANT());

		$this->invitation = InvitationDC::fromDTO($event);

		$organizers = $event->getOrganizers();
		$this->areOrganizersListed = $organizers !== null;
		$this->organizers = $organizers;
		$this->organizerUnit = \implode(', ', $event->getAdministrationUnits());

		$this->duration = self::getDuration($event);
		$this->isLongTime = self::resolveDurationCategory($this->duration) === self::DURATION_CATEGORY_LONG_TIME;

		$this->program = new ProgramDC($event->getProgram());

		$relatedWebsite = $event->getRelatedWebsite();
		$this->hasRelatedWebsite = $relatedWebsite !== null;
		$this->relatedWebsite = $relatedWebsite;
	}


	public static function getDuration(Event $event): int
	{
		$duration = $event->getDateUntil()->diff($event->getDateFrom())->days;
		\assert($duration !== false);
		return $duration + 1; // because 2018-11-30 -> 2018-11-30 is not 0, but 1 etc.
	}


	const DURATION_CATEGORY_ONE_DAY = 1;
	const DURATION_CATEGORY_WEEKEND = 2;
	const DURATION_CATEGORY_LONG_TIME = 3;

	public static function resolveDurationCategory(int $dayCount): int
	{
		return match ($dayCount)
		{
			1 => self::DURATION_CATEGORY_ONE_DAY,
			2, 3, 4, 5 => self::DURATION_CATEGORY_WEEKEND,
			default => self::DURATION_CATEGORY_LONG_TIME,
		};
	}


	private function getDateSpan(\DateTimeImmutable $dateFrom, \DateTimeImmutable $dateUntil, string $dateFormatHuman): string
	{
		$dateSpan_untilPart = $dateUntil->format($dateFormatHuman);

		$onlyOneDay = $dateFrom->format('j') === $dateUntil->format('j');
		if ($onlyOneDay) {
			return $dateSpan_untilPart;
		}

		$inSameMonth = $dateFrom->format('n') === $dateUntil->format('n');
		$inSameYear = $dateFrom->format('Y') === $dateUntil->format('Y');

		$dateSpan_fromPart = $dateFrom->format(\sprintf('j.%s%s',
			( ! $inSameMonth || ! $inSameYear) ? ' n.' : '',
			( ! $inSameYear) ? ' Y' : ''
		));

		// Czech language rules say that in case of multi-word date span there should be a space around the dash (@see http://prirucka.ujc.cas.cz/?id=810)
		$optionalSpace = '';
		if ( ! $inSameMonth) {
			$optionalSpace = ' ';
		}

		return $dateSpan_fromPart . \sprintf('%s–%s', $optionalSpace, $optionalSpace) . $dateSpan_untilPart;
	}

}
