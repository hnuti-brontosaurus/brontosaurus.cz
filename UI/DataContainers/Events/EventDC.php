<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use Brick\DateTime\LocalDate;
use DateTimeImmutable;
use HnutiBrontosaurus\BisClient\Event\Category;
use HnutiBrontosaurus\BisClient\Event\Group;
use HnutiBrontosaurus\BisClient\Event\IntendedFor;
use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\Theme\UI\Event\EventController;
use HnutiBrontosaurus\Theme\UI\Utils;
use function get_site_url;
use function implode;
use function rtrim;
use function sprintf;


final /*readonly*/ class EventDC
{
	public int $id;
	public string $link;
	public string $title;
	public bool $hasCoverPhoto;
	public ?string $coverPhotoPath;
	public string $dateStartForHumans;
	public string $dateStartForRobots;
	public bool $hasTimeStart;
	public ?string $timeStart;
	public DateTimeImmutable $dateEnd;
	public string $dateSpan;
	public int $duration;
	public PlaceDC $place;
	public AgeDC $age;
	public bool $isPaid;
	public ?string $price;
	public ContactDC $contact;
	public bool $isRegistrationRequired;
	public bool $isFull;
	public bool $isForFirstTimeAttendees;
	public InvitationDC $invitation;
	public bool $areOrganizersListed;
	public ?string $organizers;
	public ?string $organizerUnit;
	public ProgramDC $program;
	public bool $hasRelatedWebsite;
	public ?string $relatedWebsite;
	/** @var Tag[] */
	public array $tags = [];


	public function __construct(Event $event, string $dateFormatHuman, string $dateFormatRobot)
	{
		$this->id = $event->getId();
		$this->link = sprintf('%s/%s/%d/', // todo: use rather WP routing somehow
			rtrim(get_site_url(), '/'),
			EventController::PAGE_SLUG,
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
		$this->isPaid = $price !== '' && $price !== '0';
		$this->price = $price;

		$this->contact = ContactDC::fromDTO($event->getPropagation()->getContactPerson());

		$this->isRegistrationRequired = $event->getRegistration()->getIsRegistrationRequired();
		$this->isFull = $event->getRegistration()->getIsEventFull();

		$this->isForFirstTimeAttendees = $event->getIntendedFor()->equals(IntendedFor::FIRST_TIME_PARTICIPANT());

		$this->invitation = InvitationDC::fromDTO($event);

		$organizers = $event->getPropagation()->getOrganizers();
		$this->areOrganizersListed = $organizers !== null;
		$this->organizers = $organizers;
		$this->organizerUnit = implode(', ', $event->getAdministrationUnits());

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

		$dateSpan_fromPart = $dateFrom->format(sprintf('j.%s%s',
			( ! $inSameMonth || ! $inSameYear) ? ' n.' : '',
			( ! $inSameYear) ? ' Y' : ''
		));

		// Czech language rules say that in case of multi-word date span there should be a space around the dash (@see http://prirucka.ujc.cas.cz/?id=810)
		$optionalSpace = '';
		if ( ! $inSameMonth) {
			$optionalSpace = ' ';
		}

		return $dateSpan_fromPart . sprintf('%s–%s', $optionalSpace, $optionalSpace) . $dateSpan_untilPart;
	}

}
