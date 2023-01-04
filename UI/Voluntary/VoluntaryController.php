<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Voluntary;

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Request\Event\EventParameters;
use HnutiBrontosaurus\BisClient\Response\Event\Event;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventDC;
use Latte\Engine;


final class VoluntaryController implements Controller
{
	public const PAGE_SLUG = 'dobrovolnicke-akce';
	public const FILTER_KEY = 'jen';

	public function __construct(
		private string $dateFormatHuman,
		private string $dateFormatRobot,
		private BisClient $bisApiClient,
		private Base $base,
		private Engine $latte,
	) {}

	public function render(): void
	{
		// todo: use some WP way of obtaining param
		$selectedFilter = \filter_input( INPUT_GET, self::FILTER_KEY, FILTER_SANITIZE_STRING ) ?? null;
		$selectedFilter = $selectedFilter !== null && $selectedFilter !== '' ? $selectedFilter : null;

		$params = new EventParameters();
		VoluntaryFilters::apply($selectedFilter, $params);

		$eventCollection = null;
		$events = [];
		try {
			$events = $this->bisApiClient->getEvents($params);

		} catch (ConnectionToBisFailed) {
			$eventCollection = EventCollectionDC::unableToLoad($this->dateFormatHuman, $this->dateFormatRobot);
		}

		if ($eventCollection === null) {
			// in case of weekend or one day events, we need to do post filtering because BIS API can not filter by event duration
			if ($selectedFilter === VoluntaryFilters::FILTER_WEEKEND_EVENTS || $selectedFilter === VoluntaryFilters::FILTER_ONE_DAY_EVENTS) {
				$events = $this->postFilter($events, $selectedFilter);
			}

			$eventCollection = new EventCollectionDC($events, $this->dateFormatHuman, $this->dateFormatRobot);
		}

		$filters = VoluntaryFiltersDC::from(self::FILTER_KEY, $selectedFilter);

		$params = [
			'filters' => $filters,
			'eventCollection' => $eventCollection,
			'firstTimePageLink' => $this->base->getLinkFor('jedu-poprve'),
		];

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-voluntary-lazy-load', $theme->get_template_directory_uri() . '/frontend/dist/js/lazyLoad.js', [], $themeVersion);
			wp_enqueue_script('brontosaurus-voluntary-events', $theme->get_template_directory_uri() . '/frontend/dist/js/events.js', [], $themeVersion);
		});

		$this->latte->render(
			__DIR__ . '/VoluntaryController.latte',
			\array_merge($this->base->getLayoutVariables('voluntary'), $params),
		);
	}

	/**
	 * @param Event[] $events
	 * @return Event[]
	 */
	private function postFilter(array $events, string $selectedFilter): array
	{
		$filteredEvents = [];

		foreach ($events as $event) {
			if ($selectedFilter === VoluntaryFilters::FILTER_WEEKEND_EVENTS && EventDC::resolveDurationCategory(EventDC::getDuration($event)) === EventDC::DURATION_CATEGORY_WEEKEND) {
				$filteredEvents[] = $event;
			}
			if ($selectedFilter === VoluntaryFilters::FILTER_ONE_DAY_EVENTS && EventDC::resolveDurationCategory(EventDC::getDuration($event)) === EventDC::DURATION_CATEGORY_ONE_DAY) {
				$filteredEvents[] = $event;
			}
		}

		return $filteredEvents;
	}

}
