<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Homepage;

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\BisClient\Event\Request\Period;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\UI\Voluntary\VoluntaryFilters;
use Latte\Engine;


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
				->setLimit(3)
				->orderByStartDate();

			$events = $this->bisApiClient->getEvents($params);
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
