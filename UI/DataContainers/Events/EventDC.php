<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use Brick\DateTime\LocalDate;
use HnutiBrontosaurus\BisClient\Event\Category;
use HnutiBrontosaurus\BisClient\Event\Group;
use HnutiBrontosaurus\BisClient\Event\IntendedFor;
use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\Theme\UI\EventDetail\EventDetailController;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use HnutiBrontosaurus\Theme\UI\Utils;
use function get_site_url;
use function rtrim;
use function sprintf;


/**
 * @property-read int $id
 * @property-read string $link
 * @property-read string $title
 * @property-read string $dateStartForHumans
 * @property-read string $dateStartForRobots
 * @property-read bool $hasTimeStart
 * @property-read string|null $timeStart
 * @property-read \DateTimeImmutable $dateEnd
 * @property-read string $dateSpan
 * @property-read int $duration
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
 * @property-read ProgramDC $program
 * @property-read bool $hasRelatedWebsite
 * @property-read string|null $relatedWebsite
 * @property-read Tag[] $tags
 */
final class EventDC
{
	use PropertyHandler;


	private int $id;
	private string $link;
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
	/** @var Tag[] */
	public array $tags = [];


	public function __construct(Event $event, string $dateFormatHuman, string $dateFormatRobot)
	{
		$this->id = $event->getId();
		$this->link = sprintf('%s/%s/%d/', // todo: use rather WP routing somehow
			rtrim(get_site_url(), '/'),
			EventDetailController::PAGE_SLUG,
			$event->getId(),
		);
		$this->title = Utils::handleNonBreakingSpaces($event->getName());

		$coverPhotoPath = $event->getCoverPhotoPath();
		$this->hasCoverPhoto = $coverPhotoPath !== null;
		$this->coverPhotoPath = $coverPhotoPath?->getMediumSizePath(); // todo small?

		$startDateNative = $event->getStartDate()->toNativeDateTimeImmutable();
		$this->dateStartForHumans = $startDateNative->format($dateFormatHuman);
		$this->dateStartForRobots = $startDateNative->format($dateFormatRobot);
		$timeStart = $event->getStartTime();
		$this->hasTimeStart = $timeStart !== null;
		$this->timeStart = $timeStart?->toNativeDateTimeImmutable()->format('G:i');

		$this->dateEnd = $event->getEndDate()->toNativeDateTimeImmutable();
		$this->dateSpan = $this->getDateSpan($event->getStartDate(), $event->getEndDate(), $dateFormatHuman);
		$this->place = PlaceDC::fromDTO($event->getLocation());
		$this->age = AgeDC::fromDTO($event);

		$price = $event->getPropagation()->getCost();
		$this->isPaid = $price !== null;
		$this->price = $price;

		$this->contact = ContactDC::fromDTO($event->getPropagation()->getContactPerson());

		$this->isRegistrationRequired = $event->getRegistration()->getIsRegistrationRequired();
		$this->isFull = $event->getRegistration()->getIsEventFull();

		$this->isForFirstTimeAttendees = $event->getIntendedFor()->equals(IntendedFor::FIRST_TIME_PARTICIPANT());

		$this->invitation = InvitationDC::fromDTO($event);

		$organizers = $event->getPropagation()->getOrganizers();
		$this->areOrganizersListed = $organizers !== null;
		$this->organizers = $organizers;
		$this->organizerUnit = \implode(', ', $event->getAdministrationUnits());

		$this->duration = $event->getDuration();

		$this->program = new ProgramDC($event->getProgram());

		$relatedWebsite = $event->getPropagation()->getWebUrl();
		$this->hasRelatedWebsite = $relatedWebsite !== null;
		$this->relatedWebsite = $relatedWebsite;

		$this->tags = [];
		if ($this->program->isOfTypeNature) {
			$this->tags[] = new Tag('akce příroda', 'nature');
		}
		if ($this->program->isOfTypeSights) {
			$this->tags[] = new Tag('akce památky', 'sights');
		}

		$group = $event->getGroup();
		if ($this->program->isOfTypePsb) {
			if ($event->getCategory() === Category::VOLUNTEERING()) {
				$this->tags[] = new Tag('dobrovolnická');
			} elseif ($event->getCategory() === Category::EXPERIENCE()) {
				$this->tags[] = new Tag('zážitková');
			}

			$this->tags[] = new Tag('prázdninová');

		} elseif ($event->getDuration() === 1) {
			$this->tags[] = new Tag('jednodenní');
		} elseif ($group === Group::WEEKEND_EVENT()) {
			$this->tags[] = new Tag('víkendovka');
		} elseif ($group === Group::OTHER()) {
			$this->tags[] = new Tag('dlouhodobá');
		}
	}


	private function getDateSpan(LocalDate $dateFrom, LocalDate $dateUntil, string $dateFormatHuman): string
	{
		$dateFrom = $dateFrom->toNativeDateTimeImmutable();
		$dateUntil = $dateUntil->toNativeDateTimeImmutable();
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
