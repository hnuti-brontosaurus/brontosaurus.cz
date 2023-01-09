<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Future;

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\BisClientRuntimeException;
use HnutiBrontosaurus\BisClient\Request\Event\EventParameters;
use HnutiBrontosaurus\BisClient\RuntimeException;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use HnutiBrontosaurus\Theme\UI\DataContainers\MonthWrapperDC;
use Latte\Engine;


final class FutureController implements Controller
{
	public const PAGE_SLUG = 'co-se-chysta';

	public function __construct(
		private string $dateFormatHuman,
		private string $dateFormatRobot,
		private BisClient $bisApiClient,
		private Base $base,
		private Engine $latte,
	) {}


	public function render(): void
	{
		$params = new EventParameters();
		$params->orderByStartDate();

		$hasBeenUnableToLoad = false;

		try {
			$events = $this->bisApiClient->getEvents($params);

			// categorize events by month
			$months = [];
			$currentMonthWrapperDC = null;
			$lastMonth = null;

			foreach ($events as $event) {
				$monthNumber = $event->getStartDate()->getMonth();
				if ($lastMonth === null || $lastMonth !== $monthNumber) {
					if ($currentMonthWrapperDC !== null) {
						$months[] = $currentMonthWrapperDC;
					}

					$currentMonthWrapperDC = new MonthWrapperDC($monthNumber);
				}

				\assert($currentMonthWrapperDC instanceof MonthWrapperDC);
				$currentMonthWrapperDC->addEvent($event, $this->dateFormatHuman, $this->dateFormatRobot);

				$lastMonth = $monthNumber;
			}
			if ($currentMonthWrapperDC !== null) {
				$months[] = $currentMonthWrapperDC;
			}

		} catch (BisClientRuntimeException) {
			$months = [];
			$hasBeenUnableToLoad = true;
		}

		$params = [
			'months' => $months,
			'hasBeenUnableToLoad' => $hasBeenUnableToLoad,
		];

		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-future-lazy-load', $theme->get_template_directory_uri() . '/frontend/dist/js/lazyLoad.js', [], $themeVersion);
		});

		self::registerConvertMonthNumberToNameLatteFilter($this->latte);
		$this->latte->render(
			__DIR__ . '/FutureController.latte',
			\array_merge($this->base->getLayoutVariables('future'), $params),
		);
	}

	private static function registerConvertMonthNumberToNameLatteFilter(Engine $latte): void
	{
		$latte->addFilter('convertMonthNumberToName', function (int $monthNumber) {
			$monthNames = [
				1 => 'leden',
				'únor',
				'březen',
				'duben',
				'květen',
				'červen',
				'červenec',
				'srpen',
				'září',
				'říjen',
				'listopad',
				'prosinec',
			];

			if ( ! \array_key_exists($monthNumber, $monthNames)) {
				throw new \Exception('Month #' . $monthNumber . ' does not exist.');
			}

			return $monthNames[$monthNumber];
		});
	}

}
