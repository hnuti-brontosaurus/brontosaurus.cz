<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\EventDetail\DTO;


use HnutiBrontosaurus\BisApiClient\Response\Event\Registration\RegistrationQuestion;
use Nette\Http\Request;


final class ApplicationForm
{

	const FIELD_FIRST_NAME = 'firstName';
	const FIELD_LAST_NAME = 'lastName';
	const FIELD_BIRTH_DATE = 'birthDate';
	const FIELD_PHONE_NUMBER = 'phoneNumber';
	const FIELD_EMAIL_ADDRESS = 'emailAddress';
	const FIELD_QUESTION_PREFIX = 'question_'; // this is a prefix for 3 fields
	const FIELD_NOTE = 'note';


	/**
	 * @param ApplicationFormAdditionalQuestion[]|null $additionalQuestions
	 */
	private function __construct(
		private string $firstName,
		private string $lastName,
		private string $birthDate,
		private ?string $phoneNumber,
		private string $emailAddress,
		private ?array $additionalQuestions,
		private ?string $note,
	) {}


	/**
	 * @param RegistrationQuestion[]|null $questions
	 */
	public static function fromRequest(Request $httpRequest, ?array $questions): self
	{
		$questionsAndAnswers = [];

		if ($questions !== null) {
			$i = 0;
			foreach ($questions as $question) {
				$questionsAndAnswers[] = ApplicationFormAdditionalQuestion::from($question->toString(), $httpRequest->getPost(self::FIELD_QUESTION_PREFIX . $i));
				$i++;
			}
		}

		return new self(
			$httpRequest->getPost(self::FIELD_FIRST_NAME),
			$httpRequest->getPost(self::FIELD_LAST_NAME),
			$httpRequest->getPost(self::FIELD_BIRTH_DATE),
			$httpRequest->getPost(self::FIELD_PHONE_NUMBER),
			$httpRequest->getPost(self::FIELD_EMAIL_ADDRESS),
			\count($questionsAndAnswers) > 0 ? $questionsAndAnswers : null,
			$httpRequest->getPost(self::FIELD_NOTE)
		);
	}


	public function getFirstName(): string
	{
		return $this->firstName;
	}

	public function getLastName(): string
	{
		return $this->lastName;
	}

	public function getBirthDate(): string
	{
		return $this->birthDate;
	}

	public function isPhoneNumberListed(): bool
	{
		return $this->phoneNumber !== null;
	}

	public function getPhoneNumber(): ?string
	{
		return $this->phoneNumber;
	}

	public function getEmailAddress(): string
	{
		return $this->emailAddress;
	}

	public function hasAdditionalQuestions(): bool
	{
		return $this->additionalQuestions !== null;
	}

	/**
	 * @return ApplicationFormAdditionalQuestion[]|null
	 */
	public function getAdditionalQuestions(): ?array
	{
		return $this->additionalQuestions;
	}

	public function hasNote(): bool
	{
		return $this->note !== null;
	}

	public function getNote(): ?string
	{
		return $this->note;
	}


	public function toArray(): array
	{
		$values = [
			self::FIELD_FIRST_NAME => $this->getFirstName(),
			self::FIELD_LAST_NAME => $this->getLastName(),
			self::FIELD_BIRTH_DATE => $this->getBirthDate(),
			self::FIELD_PHONE_NUMBER => $this->getPhoneNumber(),
			self::FIELD_EMAIL_ADDRESS => $this->getEmailAddress(),
			self::FIELD_NOTE => $this->getNote(),
		];

		if ($this->hasAdditionalQuestions()) {
			foreach ($this->getAdditionalQuestions() as $i => $additionalQuestion) {
				$values[self::FIELD_QUESTION_PREFIX . $i] = $additionalQuestion->getAnswer();
			}
		}

		return $values;
	}

}
