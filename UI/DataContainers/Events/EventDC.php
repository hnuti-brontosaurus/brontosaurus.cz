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
 * @property-read bool $hasCoverPhoto
 * @property-read string|NULL $coverPhotoPath
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
 * @property-read int|string|null $price
 * @property-read ContactDC $contact
 * @property-read RegistrationTypeDC $registrationType
 * @property-read bool $isForFirstTimeAttendees
 * @property-read InvitationDC $invitation
 * @property-read bool $areOrganizersListed
 * @property-read string|NULL $organizers
 * @property-read bool $isOrganizerUnitListed
 * @property-read string|NULL $organizerUnit
 * @property-read ProgramDC $program
 * @property-read bool $hasRelatedWebsite
 * @property-read string|null $relatedWebsite
 */
final class EventDC
{

	use PropertyHandler;


	private function __construct(
		private int $id,
		private string $title,
		private string $slug,
		private bool $hasCoverPhoto,
		private ?string $coverPhotoPath,
		private string $dateStartForHumans,
		private string $dateStartForRobots,
		private bool $hasTimeStart,
		private ?string $timeStart,
		private \DateTimeImmutable $dateEnd,
		private string $dateSpan,
		private int $duration,
		private bool $isLongTime,
		private PlaceDC $place,
		private AgeDC $age,
		private bool $isPaid,
		private int|string|null $price,
		private ContactDC $contact,
		private RegistrationTypeDC $registrationType,
		private bool $isForFirstTimeAttendees,
		private InvitationDC $invitation,
		private bool $areOrganizersListed,
		private ?string $organizers,
		private bool $isOrganizerUnitListed, // could be probably removed once it's clear that unit is always listed
		private ?string $organizerUnit,
		private ProgramDC $program,
		private bool $hasRelatedWebsite,
		private ?string $relatedWebsite,
	) {}

	public static function fromDTO(Event $event, string $dateFormatHuman, string $dateFormatRobot)
	{
		$duration = self::getDuration($event);
		$organizer = $event->getOrganizer();

		return new self(
			$event->getId(),
			Utils::handleNonBreakingSpaces($event->getName()),
			Strings::webalize($event->getName()),
			$event->getCoverPhotoPath() !== null,
			$event->getCoverPhotoPath(),
			$event->getDateFrom()->format($dateFormatHuman),
			$event->getDateFrom()->format($dateFormatRobot),
			$event->getTimeFrom() !== null,
			$event->getTimeFrom(),
			$event->getDateUntil(),
			self::getDateSpan($event->getDateFrom(), $event->getDateUntil(), $dateFormatHuman),
			$duration,
			self::resolveDurationCategory($duration) === self::DURATION_CATEGORY_LONG_TIME,
			PlaceDC::fromDTO($event->getPlace()),
			AgeDC::fromDTO($event),
			$event->isPaid(),
			$event->isPaid() ? $event->getPrice() : null,
			ContactDC::fromDTO($event->getOrganizer()),
			RegistrationTypeDC::fromDTO($event->getRegistrationType()),
			$event->getTargetGroup()->isOfTypeFirstTimeAttendees(),
			InvitationDC::fromDTO($event->getInvitation()),
			$organizer->getOrganizers() !== null,
			$organizer->getOrganizers(),
			$organizer->getOrganizationalUnit() !== null,
			$organizer->getOrganizationalUnit()?->getName(),
			new ProgramDC($event->getProgram()),
			$event->getRelatedWebsite() !== null,
			$event->getRelatedWebsite(),
		);
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
		return match ($dayCount) {
			1 => self::DURATION_CATEGORY_ONE_DAY,
			2, 3, 4, 5 => self::DURATION_CATEGORY_WEEKEND,
			default => self::DURATION_CATEGORY_LONG_TIME,
		};
	}


	private static function getDateSpan(\DateTimeImmutable $dateFrom, \DateTimeImmutable $dateUntil, string $dateFormatHuman): string
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
