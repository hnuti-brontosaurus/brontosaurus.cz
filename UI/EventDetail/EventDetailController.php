<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\EventDetail;

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\EventNotFound;
use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\Theme\ApplicationUrlTemplate;
use HnutiBrontosaurus\Theme\NotFound;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventDC;
use Latte\Engine;
use Nette\Http\Request;
use Nette\Utils\Strings;
use function add_action;
use function array_merge;
use function get_query_var;
use function parse_url;
use function preg_match;
use function sprintf;
use function str_replace;
use function wp_enqueue_script;
use function wp_get_theme;
use const PHP_URL_HOST;


final class EventDetailController implements Controller
{

	public const PAGE_SLUG = 'detail';
	public const PARAM_PARENT_PAGE_SLUG = 'parentPageSlug';
	public const PARAM_EVENT_ID = 'eventId';

	public function __construct(
		private string $dateFormatHuman,
		private string $dateFormatRobot,
		private ApplicationUrlTemplate $applicationUrlTemplate,
		private BisClient $bisApiClient,
		private Base $base,
		private Engine $latte,
		private Request $httpRequest,
	) {}


	private Event $event;
	private bool $applicationFormSuccess = false;

	public function render(): void
	{
		$eventId = (int) get_query_var(self::PARAM_EVENT_ID);

		$this->registerFilters();

		$hasBeenUnableToLoad = false;

		try {
			$this->event = $this->bisApiClient->getEvent($eventId);
			$eventDC = new EventDC($this->event, $this->dateFormatHuman, $this->dateFormatRobot);

			// add event name to title tag (source https://stackoverflow.com/a/62410632/3668474)
			add_filter(
				'document_title_parts',
				fn(array $title) => array_merge($title, ['title' => $this->event->getName()]),
			);

		} catch (EventNotFound) {
			throw new NotFound();

		} catch (ConnectionToBisFailed) {
			$hasBeenUnableToLoad = true;
		}

//		$parentPageSlug = get_query_var(self::PARAM_PARENT_PAGE_SLUG); // does not work for some reason
		preg_match('/^\/([\w-]+).*/', $this->httpRequest->getUrl()->getPath(), $matches);
		$parentPageSlug = $matches[1];
		$params = [
			'event' => $eventDC,
			'categoryLink' => $this->base->getLinkFor($parentPageSlug),
			'hasBeenUnableToLoad' => $hasBeenUnableToLoad,
			'applicationFormSuccess' => $this->applicationFormSuccess,
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
			array_merge($this->base->getLayoutVariables('detail'), $params),
		);
	}


	private function registerFilters(): void
	{
		$this->latte->addFilter('renderWebsiteUserFriendly', static function (string $website): string {
			$hostname = parse_url($website, PHP_URL_HOST);
			$hostname = $hostname !== null ? $hostname : $website; // in case of passing "a.b.cz" to parse_url(), it fails to parse it for some reason, so just skip it

			if (Strings::startsWith($hostname, 'www.')) {
				$hostname = str_replace('www.', '', $hostname);
			}

			return $hostname;
		});

		$this->latte->addFilter('resolveRegistrationLink', fn(EventDC $event): string =>
			$this->applicationUrlTemplate->for($event->id));

		$this->latte->addFilter('formatDayCount', static fn(int $days): string =>
			match($days) {
				1 => sprintf('%d den', $days),
				2,3,4 => sprintf('%d dny', $days),
				default => sprintf('%d dní', $days),
			});

		$this->latte->addFilter('formatHourCount', static fn(int $hours): string =>
			match($hours) {
				1 => sprintf('%d hodinu', $hours),
				2,3,4 => sprintf('%d hodiny', $hours),
				default => sprintf('%d hodin', $hours),
			});
	}

}
