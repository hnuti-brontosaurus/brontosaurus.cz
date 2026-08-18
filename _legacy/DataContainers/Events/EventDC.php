<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use DateTimeImmutable;
use HnutiBrontosaurus\BisClient\Event\Category;
use HnutiBrontosaurus\BisClient\Event\Group;
use HnutiBrontosaurus\BisClient\Event\IntendedFor;
use HnutiBrontosaurus\BisClient\Event\Program;
use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\BisClient\Event\Response\Tag;


final class EventDC
{
	public readonly int $id;
	public readonly string $link;
	public readonly string $title;
	public readonly bool $hasCoverPhoto;
	public readonly ?string $coverPhotoPath;
	public readonly string $dateStartForRobots;
	public readonly bool $hasTimeStart;
	public readonly ?string $timeStart;
	public readonly string $dateSpan;
	public readonly PlaceDC $place;
	public readonly AgeDC $age;
	public readonly bool $isPaid;
	public readonly ?string $price;
	public readonly ContactDC $contact;
	public readonly bool $isPast;
	public readonly bool $isRegistrationRequired;
	public readonly bool $isFull;
	public readonly bool $isForFirstTimeAttendees;
	public readonly InvitationDC $invitation;
	public readonly bool $areOrganizersListed;
	public readonly ?string $organizers;
	public readonly ?string $organizerUnit;
	public readonly bool $hasRelatedWebsite;
	public readonly ?string $relatedWebsite;
	/** @var Label[] */
	public /*get*/ array $labels;
	/** @var string[] */
	public readonly array $tags;


	public function __construct(Event $event, string $dateFormatHuman, string $dateFormatRobot)
	{
		$this->id = $event->getId();
		$this->link = sprintf('%s/%s/%d/', // todo: use rather WP routing somehow
			rtrim(get_site_url(), '/'),
			'akce',
			$event->getId(),
		);
		$this->title = hb_handleNonBreakingSpaces($event->getName());

		$coverPhotoPath = $event->getCoverPhotoPath();
		$this->hasCoverPhoto = $coverPhotoPath !== null;
		$this->coverPhotoPath = $coverPhotoPath?->getMediumSizePath(); // todo small?

		$startDateNative = $event->getStartDate()->toNativeDateTimeImmutable();
		$this->dateStartForRobots = $startDateNative->format($dateFormatRobot);
		$timeStart = $event->getStartTime();
		$this->hasTimeStart = $timeStart !== null;
		$this->timeStart = $timeStart?->toNativeDateTimeImmutable()->format('G:i');

		$this->dateSpan = $this->getDateSpan($event->getStartDate()->toNativeDateTimeImmutable(), $event->getEndDate()->toNativeDateTimeImmutable(), $dateFormatHuman);
		$this->place = PlaceDC::fromDTO($event->getLocation());
		$this->age = AgeDC::fromDTO($event);

		$price = $event->getPropagation()->getCost();
		$this->isPaid = $price !== '' && $price !== '0';
		$this->price = $price;

		$this->contact = ContactDC::fromDTO($event->getPropagation()->getContactPerson());

		$this->isRegistrationRequired = $event->getRegistration()->getIsRegistrationRequired();
		$this->isPast = $event->getEndDate()->toNativeDateTimeImmutable()->format('Y-m-d') < (new DateTimeImmutable())->format('Y-m-d');
		$this->isFull = $event->getRegistration()->getIsEventFull();

		$this->isForFirstTimeAttendees = $event->getIntendedFor() === IntendedFor::FIRST_TIME_PARTICIPANT;

		$this->invitation = InvitationDC::fromDTO($event);

		$organizers = $event->getPropagation()->getOrganizers();
		$this->areOrganizersListed = $organizers !== null;
		$this->organizers = $organizers;
		$this->organizerUnit = implode(', ', $event->getAdministrationUnits());

		$relatedWebsite = $event->getPropagation()->getWebUrl();
		$this->hasRelatedWebsite = $relatedWebsite !== null;
		$this->relatedWebsite = $relatedWebsite;

		$this->labels = [];
		if ($event->getProgram() === Program::NATURE) {
			$this->labels[] = new Label('akce příroda', 'nature');
		}
		if ($event->getProgram() === Program::MONUMENTS) {
			$this->labels[] = new Label('akce památky', 'sights');
		}

		$group = $event->getGroup();
		if ($event->getProgram() === Program::HOLIDAYS_WITH_BRONTOSAURUS) {
			if ($event->getCategory() === Category::VOLUNTEERING) {
				$this->labels[] = new Label('dobrovolnická');
			} elseif ($event->getCategory() === Category::EXPERIENTAL) {
				$this->labels[] = new Label('zážitková');
			}

			$this->labels[] = new Label('prázdninová');

		} elseif ($event->getDuration() === 1) {
			$this->labels[] = new Label('jednodenní');
		} elseif ($group === Group::WEEKEND_EVENT) {
			$this->labels[] = new Label('víkendovka');
		} elseif ($group === Group::OTHER) {
			$this->labels[] = new Label('dlouhodobá');
		}

		$this->tags = array_map(static fn(Tag $tag) => $tag->getName(), $event->getTags());
	}


	private function getDateSpan(DateTimeImmutable $dateFrom, DateTimeImmutable $dateUntil, string $dateFormatHuman): string
	{
		$dateSpan_untilPart = $dateUntil->format($dateFormatHuman);

		$onlyOneDay = $dateFrom->format('Ymd') === $dateUntil->format('Ymd');
		if ($onlyOneDay) {
			return $dateSpan_untilPart;
		}

		$inSameMonth = $dateFrom->format('Ym') === $dateUntil->format('Ym');
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
