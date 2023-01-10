<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\EventDetail;

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Response\Event\Event;
use HnutiBrontosaurus\Theme\NotFound;
use HnutiBrontosaurus\Theme\SentryLogger;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventDC;
use HnutiBrontosaurus\Theme\UI\EventDetail\DTO\ApplicationForm;
use Latte\Engine;
use Nette\Http\Request;
use Nette\Utils\Strings;
use ReCaptcha\ReCaptcha;


final class EventDetailController implements Controller
{

	public const PAGE_SLUG = 'detail';
	public const PARAM_PARENT_PAGE_SLUG = 'parentPageSlug';
	public const PARAM_EVENT_ID = 'eventId';

	private const APPLICATION_FORM_SENT_KEY = 'sent';
	private const APPLICATION_FORM_SUCCESS_KEY = 'success';

	public function __construct(
		private string $dateFormatHuman,
		private string $dateFormatRobot,
		private string $recaptchaSiteKey,
		private string $recaptchaSecretKey,
		private ApplicationFormFacade $applicationFormFacade,
		private BisClient $bisApiClient,
		private Base $base,
		private Engine $latte,
		private Request $httpRequest,
		private SentryLogger $sentryLogger,
	) {
		$this->latte->addFilter('renderWebsiteUserFriendly', static function (string $website): string {
			$hostname = \parse_url($website, PHP_URL_HOST);
			$hostname = $hostname !== null ? $hostname : $website; // in case of passing "a.b.cz" to parse_url(), it fails to parse it for some reason, so just skip it

			if (Strings::startsWith($hostname, 'www.')) {
				$hostname = \str_replace('www.', '', $hostname);
			}

			return $hostname;
		});

		$this->latte->addFilter('resolveRegistrationLink', static function (EventDC $event): string
		{
			return \sprintf('%s/%d', 'TODO', $event->id); // todo
		});

		$this->latte->addFilter('formatDayCount', static fn(int $days): string =>
			match($days) {
				1 => \sprintf('%d den', $days),
				2,3,4 => \sprintf('%d dny', $days),
				default => \sprintf('%d dní', $days),
		});

		$this->latte->addFilter('formatHourCount', static fn(int $hours): string =>
			match($hours) {
				1 => \sprintf('%d hodinu', $hours),
				2,3,4 => \sprintf('%d hodiny', $hours),
				default => \sprintf('%d hodin', $hours),
		});
	}


	private Event $event;
	private bool $applicationFormSuccess = false;
	private ?array $applicationFormErrors = null;
	/** @var [string => string][]|null */
	private ?array $applicationFormData = null;

	public function render(): void
	{
		$eventId = (int) get_query_var(self::PARAM_EVENT_ID);

		$hasBeenUnableToLoad = false;

		try {
			$this->event = $this->bisApiClient->getEvent($eventId);
			$eventDC = new EventDC($this->event, $this->dateFormatHuman, $this->dateFormatRobot);

//			$this->processApplicationForm();

			// add event name to title tag (source https://stackoverflow.com/a/62410632/3668474)
			add_filter('document_title_parts', function (array $title) {
				return \array_merge($title, [
					'title' => $this->event->getName(),
				]);
			});

		} catch (\HnutiBrontosaurus\BisClient\NotFound) {
			throw new NotFound();

		} catch (ConnectionToBisFailed) {
			$hasBeenUnableToLoad = true;
		}

//		$parentPageSlug = get_query_var(self::PARAM_PARENT_PAGE_SLUG); // does not work for some reason
		\preg_match('/^\/([\w-]+).*/', $this->httpRequest->getUrl()->getPath(), $matches);
		$parentPageSlug = $matches[1];
		$params = [
			'event' => $eventDC,
			'categoryLink' => $this->base->getLinkFor($parentPageSlug),
			'hasBeenUnableToLoad' => $hasBeenUnableToLoad,
			'applicationFormSuccess' => $this->applicationFormSuccess,
			'applicationFormErrors' => $this->applicationFormErrors,
			'applicationFormData' => $this->applicationFormData,
			'recaptchaSiteKey' => $this->recaptchaSiteKey,
			'selfLink' => $this->base->getLinkFor($parentPageSlug) . 'detail/' . $eventId . '/',
			'firstTimePageLink' => $this->base->getLinkFor('jedu-poprve'),
			'aboutCrossroadPageLink' => $this->base->getLinkFor('o-brontosaurovi'),
		];

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-detail', $theme->get_template_directory_uri() . '/UI/EventDetail/assets/dist/js/index.js', [], $themeVersion);
		});

		$this->latte->render(
			__DIR__ . '/EventDetailController.latte',
			\array_merge($this->base->getLayoutVariables('detail'), $params),
		);
	}


	private function processApplicationForm(): void
	{
		if ( ! $this->event->getRegistrationType()->isOfTypeBrontoWeb()) { // no sense to send an application form if it is not chosen type of registration by an organizer
			return;
		}

		$sent = $this->httpRequest->getPost(self::APPLICATION_FORM_SENT_KEY) === '1'; // comes from HTTP request as a string
		$succeeded = $this->httpRequest->getQuery(self::APPLICATION_FORM_SUCCESS_KEY) === '1'; // comes from HTTP request as a string
		if ( ( ! $sent) && $succeeded) { // if not sent - there is a possible situation that someone successfully sends an application form and then wants to send another one immediately -> we have allow it
			$this->applicationFormSuccess = true;
			return;
		}

		if ( ! $sent) { // if not sent, do not evaluate
			return;
		}

		$fields = [ // field key => is field required?
			ApplicationForm::FIELD_FIRST_NAME => [
				'required' => true,
				'errorMessage' => 'Vyplň prosím své jméno.',
			],
			ApplicationForm::FIELD_LAST_NAME => [
				'required' => true,
				'errorMessage' => 'Vyplň prosím své příjmení.',
			],
			ApplicationForm::FIELD_BIRTH_DATE => [
				'required' => true,
				'errorMessage' => 'Vyplň prosím datum narození.',
			],
			ApplicationForm::FIELD_PHONE_NUMBER => [
				'required' => false,
			],
			ApplicationForm::FIELD_EMAIL_ADDRESS => [
				'required' => true,
				'errorMessage' => 'Napiš prosím svou e-mailovou adresu.',
			],
			ApplicationForm::FIELD_NOTE => [
				'required' => false,
			],
		];

		foreach ($fields as $fieldKey => $rules) {
			$fieldValue = $this->httpRequest->getPost($fieldKey);

			if ($rules['required'] && ($fieldValue === null || \trim($fieldValue) === '')) {
				if ($this->applicationFormErrors === null) {
					$this->applicationFormErrors = [];
				}

				$this->applicationFormErrors[] = $rules['errorMessage'];
			}
		}

		$recaptchaResponse = $this->httpRequest->getPost('g-recaptcha-response');
		$recaptcha = new ReCaptcha($this->recaptchaSecretKey);
		$response = $recaptcha->verify($recaptchaResponse, $_SERVER['REMOTE_ADDR']);
		if ( ! $response->isSuccess()) {
			$this->applicationFormErrors[] = 'Ověř prosím ještě jednou, že nejsi spambot.';
		}

		$questions = $this->event->getRegistrationType()->getQuestions();
		$applicationForm = ApplicationForm::fromRequest($this->httpRequest, $questions);

		if ($this->applicationFormErrors !== null && \count($this->applicationFormErrors) > 0) { // if any error, do not continue
			$this->applicationFormData = $applicationForm->toArray();
			return;
		}

		$this->applicationFormFacade->processApplicationForm($this->event, $applicationForm);

		wp_redirect(\sprintf($this->httpRequest->getUrl()->getPath() . '?%s=1', self::APPLICATION_FORM_SUCCESS_KEY));
	}

}
