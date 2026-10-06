<?php

namespace HnutiBrontosaurus\Theme\DataContainers\Events;

use DateTimeImmutable;
use HnutiBrontosaurus\BisClient\Event\Category;
use HnutiBrontosaurus\BisClient\Event\Group;
use HnutiBrontosaurus\BisClient\Event\IntendedFor;
use HnutiBrontosaurus\BisClient\Event\Program;
use HnutiBrontosaurus\BisClient\Event\Response\Event as EventFromClient;
use HnutiBrontosaurus\BisClient\Event\Response\Tag;


final class Event
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
	public readonly Place $place;
	public readonly Age $age;
	public readonly bool $isPaid;
	public readonly ?string $price;
	public readonly Contact $contact;
	public readonly bool $isPast;
	public readonly bool $isRegistrationRequired;
	public readonly bool $isFull;
	public readonly bool $isForFirstTimeAttendees;
	public readonly Invitation $invitation;
	public readonly bool $areOrganizersListed;
	public readonly ?string $organizers;
	public readonly ?string $organizerUnit;
	public readonly bool $hasRelatedWebsite;
	public readonly ?string $relatedWebsite;
	/** @var Label[] */
	public private(set) array $labels;
	/** @var string[] */
	public readonly array $tags;


	public function __construct(EventFromClient $event, string $dateFormatHuman, string $dateFormatRobot)
	{
		$this->id = $event->id;
		$this->link = sprintf('%s/%s/%d/', // todo: use rather WP routing somehow
			rtrim(get_site_url(), '/'),
			'akce',
			$event->id,
		);
		$this->title = hb_handleNonBreakingSpaces($event->name);

		$coverPhotoPath = $event->coverPhotoPath;
		$this->hasCoverPhoto = $coverPhotoPath !== null;
		$this->coverPhotoPath = $coverPhotoPath?->mediumSizePath; // todo small?

		$startDateNative = DateTimeImmutable::createFromInterface($event->startDate);
		$this->dateStartForRobots = $startDateNative->format($dateFormatRobot);
		$timeStart = $event->startTime;
		$this->hasTimeStart = $timeStart !== null;
		$this->timeStart = $timeStart;

		$this->dateSpan = $this->getDateSpan(DateTimeImmutable::createFromInterface($event->startDate), DateTimeImmutable::createFromInterface($event->endDate), $dateFormatHuman);
		$this->place = Place::fromDTO($event->location);
		$this->age = Age::fromDTO($event);

		$price = $event->propagation->cost;
		$this->isPaid = $price !== '' && $price !== '0';
		$this->price = $price;

		$this->contact = Contact::fromDTO($event->propagation->contactPerson);

		$this->isRegistrationRequired = $event->registration->isRegistrationRequired;
		$this->isPast = DateTimeImmutable::createFromInterface($event->endDate)->format('Y-m-d') < (new DateTimeImmutable())->format('Y-m-d');
		$this->isFull = $event->registration->isEventFull;

		$this->isForFirstTimeAttendees = $event->intendedFor === IntendedFor::FIRST_TIME_PARTICIPANT;

		$this->invitation = Invitation::fromDTO($event);

		$organizers = $event->propagation->organizers;
		$this->areOrganizersListed = $organizers !== null;
		$this->organizers = $organizers;
		$this->organizerUnit = implode(', ', $event->administrationUnits);

		$relatedWebsite = $event->propagation->webUrl;
		$this->hasRelatedWebsite = $relatedWebsite !== null;
		$this->relatedWebsite = $relatedWebsite;

		$this->labels = [];
		if ($event->program === Program::NATURE) {
			$this->labels[] = new Label('akce příroda', 'nature');
		}
		if ($event->program === Program::MONUMENTS) {
			$this->labels[] = new Label('akce památky', 'sights');
		}

		$group = $event->group;
		if ($event->program === Program::HOLIDAYS_WITH_BRONTOSAURUS) {
			if ($event->category === Category::VOLUNTEERING) {
				$this->labels[] = new Label('dobrovolnická');
			} elseif ($event->category === Category::EXPERIENTAL) {
				$this->labels[] = new Label('zážitková');
			}

			$this->labels[] = new Label('prázdninová');

		} elseif ($event->duration === 1) {
			$this->labels[] = new Label('jednodenní');
		} elseif ($group === Group::WEEKEND_EVENT) {
			$this->labels[] = new Label('víkendovka');
		} elseif ($group === Group::OTHER) {
			$this->labels[] = new Label('dlouhodobá');
		}

		$this->tags = array_map(static fn(Tag $tag) => $tag->name, $event->tags);
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
