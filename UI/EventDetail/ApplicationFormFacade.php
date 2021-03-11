<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\EventDetail;

use HnutiBrontosaurus\BisApiClient\BisApiClientRuntimeException;
use HnutiBrontosaurus\BisApiClient\Client;
use HnutiBrontosaurus\BisApiClient\Request\EventAttendee;
use HnutiBrontosaurus\BisApiClient\Response\Event\Event;
use HnutiBrontosaurus\BisApiClient\Response\InvalidUserInputException;
use HnutiBrontosaurus\Theme\SentryLogger;
use HnutiBrontosaurus\Theme\UI\EventDetail\DTO\ApplicationForm;
use HnutiBrontosaurus\Theme\UI\EventDetail\DTO\ApplicationFormAdditionalQuestion;
use HnutiBrontosaurus\Theme\UI\EventDetail\DTO\NotificationSender;
use Nette\Mail\Mailer;
use Tracy\Debugger;


final class ApplicationFormFacade
{

	public function __construct(
		private Client $bisApiClient,
		private Mailer $mailer,
		private EmailSettings $emailSettings,
		private SentryLogger $sentryLogger,
	) {}


	/**
	 * @throws InvalidUserInputException
	 */
	public function processApplicationForm(Event $event, ApplicationForm $applicationForm): void
	{
		$savingAttendeeToBisFailed = false;

		try {
			$this->saveToBis($event, $applicationForm);

		} catch (BisApiClientRuntimeException $e) {
			$errorMessage = \sprintf('BIS API client: could not add attendee %s to an event #%d.',
				$applicationForm->getEmailAddress(),
				$event->getId(),
			);
			Debugger::log($errorMessage); // send to tracy
			$this->sentryLogger->captureMessage($errorMessage);

			if ($e instanceof InvalidUserInputException) {
				throw $e; // wrong input => cancel form sending
			}

			$savingAttendeeToBisFailed = true;

		}

		$this->notifyOrganizer($event, $applicationForm, $savingAttendeeToBisFailed);
	}

	/**
	 * @param Event $event
	 * @param ApplicationForm $applicationForm
	 * @throws BisApiClientRuntimeException
	 */
	private function saveToBis(Event $event, ApplicationForm $applicationForm)
	{
		$this->bisApiClient->addAttendeeToEvent(new EventAttendee(
			$event->getId(),
			$applicationForm->getFirstName(),
			$applicationForm->getLastName(),
			$applicationForm->getBirthDate(),
			$applicationForm->getPhoneNumber(),
			$applicationForm->getEmailAddress(),
			$applicationForm->hasNote() ? $applicationForm->getNote() : null,
			\array_map(fn(ApplicationFormAdditionalQuestion $additionalQuestion) => $additionalQuestion->getAnswer(), $applicationForm->getAdditionalQuestions()),
		));
	}


	private function notifyOrganizer(Event $event, ApplicationForm $applicationForm, bool $savingAttendeeToBisFailed)
	{
		$bodyLines = [];

		$subject = \sprintf('Přihláška na akci %s', $event->getName());
		$bodyLines[] = \sprintf('%s (ID %d)', $subject, $event->getId()) . "\n"; // + extra line break after first line

		$bodyLines[] = 'Jméno: ' . $applicationForm->getFirstName();
		$bodyLines[] = 'Příjmení: ' . $applicationForm->getLastName();
		$bodyLines[] = 'Telefon: ' . $applicationForm->getPhoneNumber();
		$bodyLines[] = 'E-mail: ' . $applicationForm->getEmailAddress();
		$bodyLines[] = 'Datum narození: ' . $applicationForm->getBirthDate();

		if ($applicationForm->hasAdditionalQuestions()) {
			foreach ($applicationForm->getAdditionalQuestions() as $question) {
				$bodyLines[] = $question->toString();
			}
		}

		if ($applicationForm->hasNote()) {
			$bodyLines[] = 'Poznámka: ' . $applicationForm->getNote();
		}


		// send notification to the organizer

		$bodyLines_organizer = $bodyLines;

		if ($savingAttendeeToBisFailed) {
			\array_splice($bodyLines_organizer, 1, 0, "POZOR: účastníka se nepovedlo vložit do BISu. Je potřeba ho tam vložit ručně.\n");
		}

		$notificationSender = NotificationSender::prepareMessage(
			$this->mailer,
			$this->emailSettings,
			$subject,
			\implode("\n", $bodyLines_organizer),
			$applicationForm->getEmailAddress()
		);

		$notificationSender->send($event->getOrganizer()->getContactEmail());


		// send copy of the application form to the attendee

		$notificationSender = NotificationSender::prepareMessage(
			$this->mailer,
			$this->emailSettings,
			$subject,
			\implode("\n", $bodyLines),
			$event->getOrganizer()->getContactEmail()
		);

		$notificationSender->send($applicationForm->getEmailAddress());
	}

}
