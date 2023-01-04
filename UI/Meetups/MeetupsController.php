<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Meetups;

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\Request\Event\EventParameters;
use HnutiBrontosaurus\BisClient\RuntimeException;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventCollectionDC;
use Latte\Engine;


final class MeetupsController implements Controller
{
	public const PAGE_SLUG = 'setkavani-a-kluby';
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
		MeetupsFilters::apply($selectedFilter, $params);

		try {
			$events = $this->bisApiClient->getEvents($params);
			$eventCollection = new EventCollectionDC($events, $this->dateFormatHuman, $this->dateFormatRobot);

		} catch (RuntimeException) {
			$eventCollection = EventCollectionDC::unableToLoad($this->dateFormatHuman, $this->dateFormatRobot);

		}

		$params = [
			'eventCollection' => $eventCollection,
		];

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-meetups-lazy-load', $theme->get_template_directory_uri() . '/frontend/dist/js/lazyLoad.js', [], $themeVersion);
			wp_enqueue_script('brontosaurus-meetups-events', $theme->get_template_directory_uri() . '/frontend/dist/js/events.js', [], $themeVersion);
		});

		$this->latte->render(
			__DIR__ . '/MeetupsController.latte',
			\array_merge($this->base->getLayoutVariables('meetups'), $params),
		);
	}

}
