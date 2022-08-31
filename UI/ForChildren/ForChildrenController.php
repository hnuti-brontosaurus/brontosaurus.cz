<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\ForChildren;

use HnutiBrontosaurus\LegacyBisApiClient\BisApiClientRuntimeException;
use HnutiBrontosaurus\LegacyBisApiClient\Client;
use HnutiBrontosaurus\LegacyBisApiClient\Request\EventParameters;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Event;
use HnutiBrontosaurus\Theme\UI\AboutStructure\AboutStructureController;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventDC;
use Latte\Engine;


final class ForChildrenController implements Controller
{
	public const PAGE_SLUG = 'pro-deti';
	private const FILTER_KEY = 'jen';

	public function __construct(
		private string $dateFormatHuman,
		private string $dateFormatRobot,
		private Client $bisApiClient,
		private Base $base,
		private Engine $latte,
	) {}

	public function render(): void
	{
		// todo: use some WP way of obtaining param
		$selectedFilter = \filter_input( INPUT_GET, self::FILTER_KEY, FILTER_SANITIZE_STRING ) ?? null;
		$selectedFilter = $selectedFilter !== null && $selectedFilter !== '' ? $selectedFilter : null;

		$params = new EventParameters();
		ForChildrenFilters::apply($selectedFilter, $params);

		try {
			$events = $this->bisApiClient->getEvents($params);
			$eventCollection = new EventCollectionDC($events, $this->dateFormatHuman, $this->dateFormatRobot);

		} catch (BisApiClientRuntimeException $e) {
			$eventCollection = EventCollectionDC::unableToLoad($this->dateFormatHuman, $this->dateFormatRobot);

		}

		$filters = ForChildrenFiltersDC::from(self::FILTER_KEY, $selectedFilter);

		$params = [
			'filters' => $filters,
			'eventCollection' => $eventCollection,
			'aboutStructurePageLink' => $this->base->getLinkFor(AboutStructureController::PAGE_SLUG),
		];

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-forChildren-lazy-load', $theme->get_template_directory_uri() . '/frontend/dist/js/lazyLoad.js', [], $themeVersion);
			wp_enqueue_script('brontosaurus-forChildren-events', $theme->get_template_directory_uri() . '/frontend/dist/js/events.js', [], $themeVersion);
		});

		$this->latte->render(
			__DIR__ . '/ForChildrenController.latte',
			\array_merge($this->base->getLayoutVariables('forchildren'), $params),
		);
	}

}
