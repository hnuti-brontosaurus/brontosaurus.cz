<?php

use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\Filters\MeetupsFilters;
use Tracy\Debugger;

/** @var Container $hb_container defined in functions.php */
$hb_bisApiClient = $hb_container->getBisClient();
$hb_dateFormatHuman = $hb_container->getDateFormatForHuman();
$hb_dateFormatRobot = $hb_container->getDateFormatForRobot();

// todo: use some WP way of obtaining param
$selectedFilter = filter_input( INPUT_GET, 'jen' ) ?? null;
$selectedFilter = $selectedFilter !== null && $selectedFilter !== '' ? htmlspecialchars($selectedFilter) : null;

$params = new EventParameters();
MeetupsFilters::apply($selectedFilter, $params);

try {
    $events = $hb_bisApiClient->getEvents($params);
    $eventCollection = new EventCollectionDC($events, $hb_dateFormatHuman, $hb_dateFormatRobot);

} catch (ConnectionToBisFailed $e) {
    $eventCollection = EventCollectionDC::unableToLoad($hb_dateFormatHuman, $hb_dateFormatRobot);
	Debugger::log($e);
}

?><main class="hb-mbe-6" role="main" id="obsah">
	<h1 class="hb-ta-c">
		Setkávejte se s přáteli
	</h1>

	<h2 class="hb-sr-only">
		Seznam akcí
	</h2>

    <?php hb_eventList($eventCollection) ?>
</main>


<aside>
	<section class="description hb-mbe-5">
		<h1>
			Setkávání
		</h1>

		<p class="description__text">
			V Brně i Praze tvoříme prostor pro smysluplná setkávání. Potkat nás můžeš na našem Potáborovém festivalu 
			i v eko-stanech na akcích jako Rock for People, TrutnOFF či brněnský Den Země. Společně tvoříme, diskutujeme 
			a šíříme udržitelnost všude tam, kde to právě žije. Nemusíš být členem Hnutí Brontosaurus, abychom tě mezi sebou 
			rádi přivítali. Stačí si vybrat v kalendáři a prostě přijít – o zbytek už se postaráme.
		</p>
	</section>

    <?php hb_related(excludeMeetups: true) ?>
</aside>
