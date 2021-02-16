<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\DataContainers\Events;

use HnutiBrontosaurus\BisApiClient\Response\Event\Event;
use HnutiBrontosaurus\Theme\UI\PropertyHandler;
use HnutiBrontosaurus\Theme\UI\Utils;
use Nette\Utils\Strings;


/**
 * @property-read int $id
 * @property-read string $title
 * @property-read string $slug
 * @property-read EventTypeDC $type
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


	/** @var int */
	private $id;

	/** @var string */
	private $title;

	/** @var string */
	private $slug;

	/** @var bool */
	private $hasCoverPhoto = FALSE;

	/** @var string|NULL */
	private $coverPhotoPath;

	/** @var EventTypeDC */
	private $type;

	/** @var string */
	private $dateStartForHumans;

	/** @var string */
	private $dateStartForRobots;

	/** @var bool */
	private $hasTimeStart = FALSE;

	/** @var string|null */
	private $timeStart;

	/** @var \DateTimeImmutable */
	private $dateEnd;

	/** @var string */
	private $dateSpan;

	/** @var int */
	private $duration;

	/** @var bool */
	private $isLongTime;

	/** @var PlaceDC */
	private $place;

	/** @var AgeDC */
	private $age;

	/** @var bool */
	private $isPaid = FALSE;

	/** @var string|NULL */
	private $price;

	/** @var ContactDC */
	private $contact;

	/** @var RegistrationTypeDC */
	private $registrationType;

	/** @var bool */
	private $isForFirstTimeAttendees = FALSE;

	/** @var InvitationDC */
	private $invitation;

	/** @var bool */
	private $areOrganizersListed = FALSE;

	/** @var string|NULL */
	private $organizers;

	/** @var ProgramDC */
	private $program;

	/** @var bool */
	private $hasRelatedWebsite = false;

	/** @var string|null */
	private $relatedWebsite;


	/**
	 * @param string $dateFormatHuman
	 * @param string $dateFormatRobot
	 */
	public function __construct(Event $event, $dateFormatHuman, $dateFormatRobot)
	{
		$this->id = $event->getId();
		$this->title = Utils::handleNonBreakingSpaces($event->getName());
		$this->slug = Strings::webalize($event->getName());

		if ($event->hasCoverPhoto()) {
			$this->hasCoverPhoto = TRUE;
			$this->coverPhotoPath = $event->getCoverPhotoPath();
		}


		$this->type = new EventTypeDC($event->getType(), 'todo-some-icon');
		$this->dateStartForHumans = $event->getDateFrom()->format($dateFormatHuman);
		$this->dateStartForRobots = $event->getDateFrom()->format($dateFormatRobot);

		if ($event->hasTimeFrom()) {
			$this->hasTimeStart = TRUE;
			$this->timeStart = $event->getTimeFrom();
		}

		$this->dateEnd = $event->getDateUntil();
		$this->dateSpan = $this->getDateSpan($event->getDateFrom(), $event->getDateUntil(), $dateFormatHuman);
		$this->place = PlaceDC::fromDTO($event->getPlace());
		$this->age = AgeDC::fromDTO($event);

		if ($event->isPaid()) {
			$this->isPaid = true;
			$this->price  = $event->getPrice();
		}

		$this->contact = ContactDC::fromDTO($event->getOrganizer());

		$this->registrationType = RegistrationTypeDC::fromDTO($event->getRegistrationType());

		$this->isForFirstTimeAttendees = $event->getTargetGroup()->isOfTypeFirstTimeAttendees();

		$this->invitation = InvitationDC::fromDTO($event->getInvitation());

		if ($event->getOrganizer()->areOrganizersListed()) {
			$this->areOrganizersListed = TRUE;
			$this->organizers = $event->getOrganizer()->getOrganizers();
		}

		$this->duration = self::getDuration($event);
		$this->isLongTime = self::resolveDurationCategory($this->duration) === self::DURATION_CATEGORY_LONG_TIME;

		$this->program = new ProgramDC($event->getProgram());

		if ($event->hasRelatedWebsite()) {
			$this->hasRelatedWebsite = true;
			$this->relatedWebsite = $event->getRelatedWebsite();
		}
	}


	/**
	 * @param Event $event
	 * @return int
	 */
	public static function getDuration(Event $event)
	{
		$duration = $event->getDateUntil()->diff($event->getDateFrom())->days;
		\assert($duration !== FALSE);
		return $duration + 1; // because 2018-11-30 -> 2018-11-30 is not 0, but 1 etc.
	}

	const DURATION_CATEGORY_ONE_DAY = 1;
	const DURATION_CATEGORY_WEEKEND = 2;
	const DURATION_CATEGORY_LONG_TIME = 3;
	/**
	 * @param int $dayCount
	 * @return int
	 */
	public static function resolveDurationCategory($dayCount)
	{
		switch ($dayCount) {
			case 1:
				return self::DURATION_CATEGORY_ONE_DAY;
				break;

			case 2:
			case 3:
			case 4:
			case 5:
				return self::DURATION_CATEGORY_WEEKEND;
				break;

			default:
				return self::DURATION_CATEGORY_LONG_TIME;
				break;
		}
	}


	/**
	 * @param \DateTimeImmutable $dateFrom
	 * @param \DateTimeImmutable $dateUntil
	 * @param string $dateFormatHuman
	 * @return string
	 */
	private function getDateSpan(\DateTimeImmutable $dateFrom, \DateTimeImmutable $dateUntil, $dateFormatHuman)
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
