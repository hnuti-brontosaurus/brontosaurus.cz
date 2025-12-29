<?php

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\BisClient\Event\Request\Period;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\DataContainers\MonthWrapperDC;
use Tracy\Debugger;

/** @var Container $hb_container defined in functions.php */
$hb_bisApiClient = $hb_container->getBisClient();
$hb_dateFormatHuman = $hb_container->getDateFormatForHuman();
$hb_dateFormatRobot = $hb_container->getDateFormatForRobot();

function hb_convertMonthNumberToName(int $monthNumber): string
{
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

    if ( ! array_key_exists($monthNumber, $monthNames)) {
        throw new Exception('Month #' . $monthNumber . ' does not exist.');
    }

    return $monthNames[$monthNumber];
}

$params = new EventParameters();
$params->orderByStartDate();
$params->setPeriod(Period::FUTURE_ONLY());

$hasBeenUnableToLoad = false;

try {
    $events = $hb_bisApiClient->getEvents($params);

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
        $currentMonthWrapperDC->addEvent($event, $hb_dateFormatHuman, $hb_dateFormatRobot);

        $lastMonth = $monthNumber;
    }
    if ($currentMonthWrapperDC !== null) {
        $months[] = $currentMonthWrapperDC;
    }

} catch (ConnectionToBisFailed $e) {
    $months = [];
    $hasBeenUnableToLoad = true;
    Debugger::log($e);
}

?><main role="main">
	<h1 class="hb-ta-c hb-mbe-4">
		Co se děje u Brontosaurů?
	</h1>


	<?php if ($hasBeenUnableToLoad): ?>
		<div class="noResults">
			Promiňte, zrovna nám vypadl systém, kde máme uloženy všechny informace o plánovaných akcích.
			Zkuste to prosím za chvilku znovu.
		</div>
	<?php else: ?>
		<div class="events">
        <?php 
        $counter = 0; 
        foreach ($months as $month): 
        ?>
			<h2 class="hb-ta-c hb-tt-u hb-mbe-4">
                <?php echo hb_convertMonthNumberToName($month->monthNumber) ?>
			</h2>

			<div class="hb-eventList<?php if ($counter < (count($months) - 1)): ?> hb-mbe-6<?php endif; ?>">
                <?php hb_eventList($month->events, inFutureView: true) ?>
			</div>
        <?php 
        $counter++;
        endforeach; 
        ?>
		</div>
	<?php endif; ?>
</main>
