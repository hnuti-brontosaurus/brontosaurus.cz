<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event;

use Grifart\Enum\MissingValueDeclarationException;
use HnutiBrontosaurus\LegacyBisApiClient\BadUsageException;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation\Food;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation\Invitation;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation\Photo;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation\Presentation;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Registration\RegistrationQuestion;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Registration\RegistrationType;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Registration\RegistrationTypeEnum;
use HnutiBrontosaurus\LegacyBisApiClient\Response\RegistrationTypeException;


final class Event
{

	private function __construct(
		private int $id,
		private string $name,
		private ?string $coverPhotoPath,
		private \DateTimeImmutable $dateFrom,
		private \DateTimeImmutable $dateUntil,
		private string $type,
		private Program $program,
		private Place $place,
		private RegistrationType $registrationType,
		private ?int $ageFrom,
		private ?int $ageUntil,
		private int|string $price, // single number or interval
		private Organizer $organizer,
		private TargetGroup $targetGroup,
		private Invitation $invitation,
		private ?string $timeFrom,
		private ?string $programDescription,
		private ?string $notes,
		private ?string $relatedWebsite,
	) {}


	/**
	 * @param string[] $data Everything is string as it comes from HTTP response body.
	 * @throws RegistrationTypeException
	 * @throws BadUsageException
	 */
	public static function fromResponseData(array $data): static
	{
		// program
		try {
			$programSlug = (isset($data['program_id']) && $data['program_id'] !== '')
				? ProgramType::fromScalar($data['program_id'])
				: ProgramType::NONE();
		} catch (MissingValueDeclarationException) {
			$programSlug = ProgramType::NONE();
		}
		$programName = (isset($data['program']) && $data['program'] !== '') ? $data['program'] : null;
		$program = Program::from($programSlug, $programName);

		// place
		$placeName = $data['lokalita'];
		$placeAlternativeName = (isset($data['lokalita_misto']) && $data['lokalita_misto'] !== '') ? $data['lokalita_misto'] : null;
		$placeCoordinates = (isset($data['lokalita_gps']) && $data['lokalita_gps'] !== '') ? $data['lokalita_gps'] : null;
		$place = Place::from(
			$placeAlternativeName !== null
				? $placeAlternativeName // it looks like alternative names are more specific
				: $placeName,
			$placeCoordinates !== null
				? $placeCoordinates
				: null,
		);

		// registration
		try {
			$registrationType = RegistrationTypeEnum::fromScalar((int) $data['prihlaska']);
		} catch (MissingValueDeclarationException) {
			$registrationType = RegistrationTypeEnum::DISABLED(); // silent fallback
		}

		$webRegistrationQuestion1 = (isset($data['add_info_title']) && $data['add_info_title'] !== '') ? $data['add_info_title'] : null;
		$webRegistrationQuestion2 = (isset($data['add_info_title_2']) && $data['add_info_title_2'] !== '') ? $data['add_info_title_2'] : null;
		$webRegistrationQuestion3 = (isset($data['add_info_title_3']) && $data['add_info_title_3'] !== '') ? $data['add_info_title_3'] : null;
		$contactEmail = $data['kontakt_email'];
		$registrationCustomUrl = (isset($data['kontakt_url']) && $data['kontakt_url'] !== '') ? $data['kontakt_url'] : null;
		$registrationQuestions = \array_filter([ // exclude all null items
			$webRegistrationQuestion1,
			$webRegistrationQuestion2,
			$webRegistrationQuestion3,
		], fn($v, $k) => $v !== null, \ARRAY_FILTER_USE_BOTH);
		$registrationType = RegistrationType::from(
			$registrationType,
			\array_map(fn(string $question) => RegistrationQuestion::from($question), $registrationQuestions),
			$contactEmail,
			$registrationCustomUrl,
		);

		// price
		$price = 0;
		if (isset($data['poplatek']) && $data['poplatek'] !== '') {
			$price = $data['poplatek'];

			if (\preg_match('|^[0-9]+$|', $price)) {
				$price = (int) $price;
			}
		}

		// organizers
		$organizationalUnitId = (isset($data['porada_id']) && $data['porada_id'] !== '') ? ((int) $data['porada_id']) : null;
		$organizationalUnitName= (isset($data['porada']) && $data['porada'] !== '') ? $data['porada'] : null;
		$organizers = (isset($data['org']) && $data['org'] !== '') ? $data['org'] : null;
		$contactPersonName = (isset($data['kontakt']) && $data['kontakt'] !== '') ? $data['kontakt'] : null;
		$contactPhone = $data['kontakt_telefon'];
		$responsiblePerson = (isset($data['odpovedna']) && $data['odpovedna'] !== '') ? $data['odpovedna'] : null;
		$organizer = Organizer::from(
			($organizationalUnitId !== null && $organizationalUnitName !== null)
				? OrganizerOrganizationalUnit::from($organizationalUnitId, $organizationalUnitName)
				: null,
			$responsiblePerson,
			$organizers,
			$contactPersonName,
			$contactPhone,
			$contactEmail,
		);


		// invitation

		// BIS API returns "0", "1", "2" etc. for real options and "" when nothing is set
		$food = (isset($data['strava']) && $data['strava'] !== '')
			? Food::fromScalar((int) $data['strava'])
			: Food::NOT_LISTED();

		/** @var Photo[] $invitationPresentationPhotos */
		$invitationPresentationPhotos = [];
		for ($i = 1; $i <= 6; $i++) {
			if (isset($data['ochutnavka_' . $i]) && $data['ochutnavka_' . $i] !== '') {
				$invitationPresentationPhotos[] = Photo::from($data['ochutnavka_' . $i]);
			}
		}

		$invitationOrganizationalInformation = (isset($data['text_info']) && $data['text_info'] !== '') ? $data['text_info'] : ''; // this will not be needed in BIS but now it has to be there as somehow obligatory fields are not required anymore in old BIS
		$invitationIntroduction = (isset($data['text_uvod']) && $data['text_uvod'] !== '') ? $data['text_uvod'] : ''; // this will not be needed in BIS but now it has to be there as somehow obligatory fields are not required anymore in old BIS
		$invitationPresentationText = (isset($data['text_mnam']) && $data['text_mnam'] !== '') ? $data['text_mnam'] : null;
		$invitationWorkDescription = (isset($data['text_dobr']) && $data['text_dobr'] !== '') ? $data['text_dobr'] : null;
		$workHoursPerDay = (isset($data['pracovni_doba']) && $data['pracovni_doba'] !== '') ? ((int) $data['pracovni_doba']) : null;
		$accommodation = (isset($data['ubytovani']) && $data['ubytovani'] !== '') ? $data['ubytovani'] : null;
		$invitation = Invitation::from(
			$invitationIntroduction,
			$invitationOrganizationalInformation,
			$accommodation,
			$food,
			$invitationWorkDescription,
			$workHoursPerDay,
			($invitationPresentationText !== null || \count($invitationPresentationPhotos) > 0)
				? Presentation::from($invitationPresentationText, $invitationPresentationPhotos)
				: null,
		);


		// related website
		$relatedWebsite = (isset($data['web']) && $data['web'] !== '') ? $data['web'] : null;
		$_relatedWebsite = null;
		if ($relatedWebsite !== null) {
			if ( ! self::startsWith($relatedWebsite, 'http')) { // count with no protocol typed URLs
				$relatedWebsite = 'http://' . $relatedWebsite;
			}

			$_relatedWebsite = $relatedWebsite;
		}

		return new self(
			(int) $data['id'],
			$data['nazev'],
			(isset($data['foto_hlavni']) && $data['foto_hlavni'] !== '') ? $data['foto_hlavni'] : null,
			\DateTimeImmutable::createFromFormat('Y-m-d', $data['od']),
			\DateTimeImmutable::createFromFormat('Y-m-d', $data['do']),
			$data['typ'],
			$program,
			$place,
			$registrationType,
			(isset($data['vek_od']) && $data['vek_od'] !== '') ? ((int) $data['vek_od']) : null,
			(isset($data['vek_do']) && $data['vek_do'] !== '') ? ((int) $data['vek_do']) : null,
			$price,
			$organizer,
			TargetGroup::from((int) $data['prokoho']),
			$invitation,
			(isset($data['sraz']) && $data['sraz'] !== '') ? $data['sraz'] : null,
			(isset($data['popis_programu']) && $data['popis_programu'] !== '') ? $data['popis_programu'] : null,
			(isset($data['jak_se_prihlasit']) && $data['jak_se_prihlasit'] !== '') ? $data['jak_se_prihlasit'] : null,
			$_relatedWebsite,
		);
	}


	public function getId(): int
	{
		return $this->id;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function getCoverPhotoPath(): ?string
	{
		return $this->coverPhotoPath;
	}


	public function hasCoverPhoto(): bool
	{
		return $this->coverPhotoPath !== null;
	}


	public function getDateFrom(): \DateTimeImmutable
	{
		return $this->dateFrom;
	}


	public function getDateUntil(): \DateTimeImmutable
	{
		return $this->dateUntil;
	}


	public function getType(): string
	{
		return $this->type;
	}


	public function getProgram(): Program
	{
		return $this->program;
	}


	public function getPlace(): Place
	{
		return $this->place;
	}


	public function getRegistrationType(): RegistrationType
	{
		return $this->registrationType;
	}


	public function getAgeFrom(): ?int
	{
		return $this->ageFrom;
	}


	public function getAgeUntil(): ?int
	{
		return $this->ageUntil;
	}


	public function getPrice(): int|string
	{
		return $this->price;
	}


	public function isPaid(): bool
	{
		return $this->price !== 0;
	}


	public function getOrganizer(): Organizer
	{
		return $this->organizer;
	}


	public function getTargetGroup(): TargetGroup
	{
		return $this->targetGroup;
	}


	public function getInvitation(): Invitation
	{
		return $this->invitation;
	}


	public function hasTimeFrom(): bool
	{
		return $this->timeFrom !== null;
	}


	public function getTimeFrom(): ?string
	{
		return $this->timeFrom;
	}


	public function getProgramDescription(): ?string
	{
		return $this->programDescription;
	}


	public function getNotes(): ?string
	{
		return $this->notes;
	}


	public function hasRelatedWebsite(): bool
	{
		return $this->relatedWebsite !== null;
	}


	public function getRelatedWebsite(): ?string
	{
		return $this->relatedWebsite;
	}


	/**
	 * Extracted from \Nette\Utils\Strings (v2.3)
	 * Starts the $haystack string with the prefix $needle?
	 */
	private static function startsWith(string $haystack, string $needle): bool
	{
		return \strncmp($haystack, $needle, \strlen($needle)) === 0;
	}

}
