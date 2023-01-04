<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Event;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use HnutiBrontosaurus\Theme\UI\Utils;
use Nette\Utils\Strings;


/**
 * @property-read int $id
 * @property-read string $title
 * @property-read string $slug
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
 * @property-read string $price
 * @property-read ContactDC $contact
 * @property-read RegistrationTypeDC $registrationType
 * @property-read bool $isForFirstTimeAttendees
 * @property-read InvitationDC $invitation
 * @property-read bool $areOrganizersListed
 * @property-read string|NULL $organizers
 * @property-read bool $isOrganizerUnitListed
 * @property-read string|NULL $organizerUnit
 * @property-read bool $hasCoverPhoto
 * @property-read string|NULL $coverPhotoPath
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
	private string $slug;
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
	private string|int|null $price;
	private ContactDC $contact;
	private RegistrationTypeDC $registrationType;
	private bool $isForFirstTimeAttendees;
	private InvitationDC $invitation;
	private bool $areOrganizersListed;
	private ?string $organizers;
	private bool $isOrganizerUnitListed; // could be probably removed once it's clear that unit is always listed
	private ?string $organizerUnit;
	private ProgramDC $program;
	private bool $hasRelatedWebsite;
	private ?string $relatedWebsite;


	public function __construct(Event $event, string $dateFormatHuman, string $dateFormatRobot)
	{
		$this->id = $event->getId();
		$this->title = Utils::handleNonBreakingSpaces($event->getName());
		$this->slug = Strings::webalize($event->getName());

		$this->hasCoverPhoto = $event->getCoverPhotoPath() !== null;
		$this->coverPhotoPath = $event->getCoverPhotoPath();

		$this->dateStartForHumans = $event->getDateFrom()->format($dateFormatHuman);
		$this->dateStartForRobots = $event->getDateFrom()->format($dateFormatRobot);

		$this->hasTimeStart = $event->getTimeFrom() !== null;
		$this->timeStart = $event->getTimeFrom();

		$this->dateEnd = $event->getDateUntil();
		$this->dateSpan = $this->getDateSpan($event->getDateFrom(), $event->getDateUntil(), $dateFormatHuman);
		$this->place = PlaceDC::fromDTO($event->getPlace());
		$this->age = AgeDC::fromDTO($event);

		$this->isPaid = $event->getPrice() !== null;
		$this->price  = $event->getPrice();

		$this->contact = ContactDC::fromDTO($event->getOrganizer());

		$this->registrationType = RegistrationTypeDC::fromDTO($event->getRegistrationType());

		$this->isForFirstTimeAttendees = $event->getTargetGroup()->isOfTypeFirstTimeAttendees();

		$this->invitation = InvitationDC::fromDTO($event->getInvitation());

		$organizer = $event->getOrganizer();
		$areOrganizersListed = $organizer->getOrganizers() !== null;
		$this->areOrganizersListed = $areOrganizersListed;
		$this->organizers = $organizer->getOrganizers();
		$unit = $organizer->getOrganizationalUnit();
		$this->isOrganizerUnitListed = $areOrganizersListed && $unit !== null;
		$this->organizerUnit = $areOrganizersListed && $unit !== null ? $unit->getName() : null;

		$this->duration = self::getDuration($event);
		$this->isLongTime = self::resolveDurationCategory($this->duration) === self::DURATION_CATEGORY_LONG_TIME;

		$this->program = new ProgramDC($event->getProgram());

		$this->hasRelatedWebsite = $event->getRelatedWebsite() !== null;
		$this->relatedWebsite = $event->getRelatedWebsite();
	}


	public static function getDuration(Event $event): int
	{
		$duration = $event->getDateUntil()->diff($event->getDateFrom())->days;
		\assert($duration !== false);
		return $duration + 1; // because 2018-11-30 -> 2018-11-30 is not 0, but 1 etc.
	}

	public const DURATION_CATEGORY_ONE_DAY = 1;
	public const DURATION_CATEGORY_WEEKEND = 2;
	public const DURATION_CATEGORY_LONG_TIME = 3;

	public static function resolveDurationCategory(int $dayCount): int
	{
		return match ($dayCount) {
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
