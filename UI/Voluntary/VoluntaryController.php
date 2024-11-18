<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Voluntary;

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\BisClient\Event\Response\Event;
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
		$selectedFilter = \filter_input( INPUT_GET, self::FILTER_KEY ) ?? null;
		$selectedFilter = $selectedFilter !== null && $selectedFilter !== '' ? \htmlspecialchars($selectedFilter :) null;

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
			// in case of one-day events, we need to do post-filtering because BIS doesn't distinct on domain level
			if ($selectedFilter === VoluntaryFilters::FILTER_ONE_DAY_EVENTS) {
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
			if ($selectedFilter === VoluntaryFilters::FILTER_ONE_DAY_EVENTS && $event->getDuration() === 1) {
				$filteredEvents[] = $event;
			}
		}

		return $filteredEvents;
	}

}
