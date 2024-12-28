<?php

use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\Filters\MeetupsFilters;

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

} catch (ConnectionToBisFailed) {
    $eventCollection = EventCollectionDC::unableToLoad($hb_dateFormatHuman, $hb_dateFormatRobot);
}

?><main class="hb-events hb-mbe-6" role="main" id="obsah">
	<h1 class="hb-events__heading">
		Setkávejte&nbsp;se s&nbsp;přáteli
	</h1>

	<h2 class="screenreaders-only">
		Seznam akcí
	</h2>

    <?php hb_eventList($eventCollection) ?>
</main>


<aside>
	<section class="description hb-mbe-5">
		<h1>
			Setkávání a&nbsp;kluby
		</h1>

		<p class="description__text">
			V&nbsp;Brně a&nbsp;Praze pořádáme kluby na&nbsp;různá témata. Připravujeme přednášky, workshopy,
			ochutnávky jídel, sport či&nbsp;deskovky. V&nbsp;minulosti jsme třeba hráli frisbee, volejbal
			nebo na&nbsp;kytary, cvičili acrojogu a&nbsp;vyráběli domácí kosmetiku. Nemusíš&nbsp;být členem
			Hnutí Brontosaurus, moc&nbsp;rádi mezi&nbsp;sebe přivítáme nové lidi. Stačí si&nbsp;vybrat klub
			a&nbsp;přijít mezi&nbsp;nás, o&nbsp;zbytek už se&nbsp;postaráme.
		</p>
	</section>

    <?php hb_related(excludeMeetups: true) ?>
</aside>
