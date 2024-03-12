<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Homepage;

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\BisClient\Event\Request\Period;
use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\UI\Voluntary\VoluntaryFilters;
use Latte\Engine;
use function array_filter;
use function array_slice;


final class HomepageController implements Controller
{

	public function __construct(
		private string $dateFormatHuman,
		private string $dateFormatRobot,
		private BisClient $bisApiClient,
		private Base $base,
		private Engine $latte,
	) {}


	public function render(): void
	{
		try {
			$params = new EventParameters();

			// for listing only voluntary events use voluntary filters
			VoluntaryFilters::apply(selectedFilter: null, parameters: $params);

			$params
				->setPeriod(Period::FUTURE_ONLY())
				->setLimit(6) // we need three, but because of post-filtering, we need to load more
				->orderByStartDate();
			$events = $this->bisApiClient->getEvents($params);

			// post-filter: leave out full events
			$events = array_filter(
				$events,
				static fn(Event $event) => ! $event->getRegistration()->getIsEventFull(),
			);

			// keep only first three
			$events = array_slice($events, offset: 0, length: 3, preserve_keys: false);

			$eventCollection = new EventCollectionDC($events, $this->dateFormatHuman, $this->dateFormatRobot);

		} catch (ConnectionToBisFailed) {
			$eventCollection = EventCollectionDC::unableToLoad($this->dateFormatHuman, $this->dateFormatRobot);
		}

		$params = [
			'aboutCrossroadPageLink' => $this->base->getLinkFor('o-brontosaurovi'),
			'eventCollection' => $eventCollection,
		];

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-homepage-lazy-load', $theme->get_template_directory_uri() . '/frontend/dist/js/lazyLoad.js', [], $themeVersion);
			wp_enqueue_script('brontosaurus-homepage-references', $theme->get_template_directory_uri() . '/frontend/dist/js/references.js', [], $themeVersion);
		});

		$this->latte->render(
			__DIR__ . '/HomepageController.latte',
			\array_merge($this->base->getLayoutVariables('home'), $params),
		);
	}

}
