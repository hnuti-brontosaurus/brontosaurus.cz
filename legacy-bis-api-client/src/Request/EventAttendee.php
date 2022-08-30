<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Request;


final class EventAttendee extends Parameters
{

	/**
	 * @param string[] $questionAnswers
	 */
	public function __construct(
		int $eventId,
		string $firstName,
		string $lastName,
		string $birthDate,
		string $phoneNumber,
		string $emailAddress,
		?string $note,
		array $questionAnswers,
	) {
		parent::__construct([
			self::PARAM_QUERY => 'prihlaska',
			'akce' => $eventId,
			'jmeno' => $firstName,
			'prijmeni' => $lastName,
			'telefon' => $phoneNumber,
			'email' => $emailAddress,
			'datum_narozeni' => $birthDate,
			'poznamka' => $note,
		]);


		if (\count($questionAnswers) === 0) {
			return;
		}

		// currently 3 questions are supported
		$i = 1;
		foreach ($questionAnswers as $questionAnswer) {
			$this->params['add_info' . ($i >= 2 ? '_' . $i : '')] = $questionAnswer; // key syntax is `add_info` for first key and `add_info_X` for any other
			$i++;
		}
	}

}
