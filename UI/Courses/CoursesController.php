<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Courses;

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Request\Event\EventParameters;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventCollectionDC;
use Latte\Engine;


final class CoursesController implements Controller
{
	public const PAGE_SLUG = 'kurzy-a-prednasky';
	private const FILTER_KEY = 'jen';

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
		CoursesFilters::apply($selectedFilter, $params);

		try {
			$events = $this->bisApiClient->getEvents($params);
			$eventCollection = new EventCollectionDC($events, $this->dateFormatHuman, $this->dateFormatRobot);

		} catch (ConnectionToBisFailed) {
			$eventCollection = EventCollectionDC::unableToLoad($this->dateFormatHuman, $this->dateFormatRobot);

		}

		$filters = CoursesFiltersDC::from(self::FILTER_KEY, $selectedFilter);

		$params = [
			'filters' => $filters,
			'eventCollection' => $eventCollection,
			'highSchoolPageLink' => $this->base->getLinkFor('programy-pro-stredni-skoly'),
		];

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-courses-lazy-load', $theme->get_template_directory_uri() . '/frontend/dist/js/lazyLoad.js', [], $themeVersion);
			wp_enqueue_script('brontosaurus-courses-events', $theme->get_template_directory_uri() . '/frontend/dist/js/events.js', [], $themeVersion);
		});

		$this->latte->render(
			__DIR__ . '/CoursesController.latte',
			\array_merge($this->base->getLayoutVariables('courses'), $params),
		);
	}

}
