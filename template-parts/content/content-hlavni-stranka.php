<?php

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\BisClient\Event\Request\Period;
use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\Filters\VoluntaryFilters;

/** @var Container $hb_container defined in functions.php */

$hb_bisApiClient = $hb_container->getBisClient();
$hb_dateFormatForHuman = $hb_container->getDateFormatForHuman();
$hb_dateFormatForRobot = $hb_container->getDateFormatForRobot();

function hb_banner(): ?\stdCLass
{
    $heading = get_option('homepage-banner_heading');
    $subheading = get_option('homepage-banner_subheading');
    $image = get_option('homepage-banner_image');
    $link = get_option('homepage-banner_link');

    if ($image === '') {
        return null;
    }

    return (object) [
        'heading' => $heading,
        'subheading' => $subheading ?: null,
        'image' => $image,
        'link' => $link ?: null,
    ];
}

try {
    $params = new EventParameters();

    // for listing only voluntary events use voluntary filters
    VoluntaryFilters::apply(selectedFilter: null, parameters: $params);

    $params
        ->setPeriod(Period::FUTURE_ONLY())
        ->setLimit(6) // we need three, but because of post-filtering, we need to load more
        ->orderByStartDate();
    $events = $hb_bisApiClient->getEvents($params);

    // post-filter: leave out full events
    $events = array_filter(
        $events,
        static fn(Event $event) => ! $event->getRegistration()->getIsEventFull(),
    );

    // keep only first three
    $events = array_slice($events, offset: 0, length: 3, preserve_keys: false);

    $eventCollection = new EventCollectionDC($events, $hb_dateFormatForHuman, $hb_dateFormatForRobot);

} catch (ConnectionToBisFailed) {
    $eventCollection = EventCollectionDC::unableToLoad($hb_dateFormatForHuman, $hb_dateFormatForRobot);
}

$hb_banner = hb_banner();

?><main class="homepage hb-mbns-5" role="main">
	<?php
    if ($hb_banner !== null):
    $hb_banner_tag = $hb_banner->link !== null ? 'a' : 'div';
    ?>
	<<?php echo $hb_banner_tag ?> class="hb-block-banner hb-mbs-5 hb-pbs-2 hb-mbe-5" <?php if ($hb_banner->link !== null): ?>href="<?php echo $hb_banner->link ?>" rel="noopener"<?php endif; ?> style="--hb-block-banner-background-url: url('<?php echo $hb_banner->image ?>')">
		<div class="hb-block-banner__textContainer">
			<div class="hb-block-banner__text hb-mbs-3">
				<div class="hb-fs-xl hb-mbe-2 hb-fw-b"><?php echo $hb_banner->heading ?></div>
				<?php if ($hb_banner->subheading !== null): ?><div class="hb-mbe-2"><?php echo $hb_banner->subheading ?></div><?php endif; ?>
				<span class="hb-block-banner__button hb-block-banner__button--standalone hb-action hb-action--primary hb-action--paw">více info</span>
				<span class="hb-block-banner__button hb-block-banner__button--inline hb-fs-xs" aria-hidden="true">více info »</span>
			</div>
		</div>
	</<?php echo $hb_banner_tag ?>>
	<?php endif; ?>

	<h1 class="<?php if ($hb_banner === null): ?>hb-mbs-4 <?php endif; ?>hb-ta-c hb-mbe-3 hb-lg-mbe-3">
		Dobrovolnické a zážitkové akce
	</h1>

	<p class="hb-block-text hb-ta-c hb-mi-auto hb-mbe-4">
		Pojeď na víkendovou nebo prázdninovou akci vstříc novým zážitkům a přátelstvím.
		Vyraz s námi pomáhat přírodě a památkám u nás i v zahraničí.
	</p>

	<div class="hb-mbe-4">
        <?php hb_eventList($eventCollection, smaller: true) ?>
	</div>

	<div class="hb-d-f hb-fd-r hb-jc-c hb-ji-c hb-ai-c hb-g-4 hb-mbe-5">
		<a class="hb-action hb-action--primary hb-action--paw" href="/dobrovolnicke-akce#obsah">
			zobrazit další
		</a>

		<a class="hb-d-ib hb-fs-xs hb-td-u" href="/dobrovolnicke-akce#co-zazijes-na-dobrovolnicke-akci">
			více informací o akcích
		</a>
	</div>
</main>

<aside>
	<div class="homepage-related hb-mbe-5">
        <?php hb_related(excludeVoluntary: true, headingText: 'A to není všechno…') ?>
	</div>

	<section>
		<h1 class="hb-mbe-3">
			Seznamte se s Brontosaurem
		</h1>

		<div class="homepage__subheading">
			* 1974
		</div>

		<div class="hb-mw-40 hb-mi-auto hb-mbe-5">
			<iframe class="hb-d-b hb-is-100 hb-ar-169 hb-br" src="https://www.youtube.com/embed/ksdnd8Bft1Q" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div>

		<div class="hb-mbe-4">
			<h2>Řekli o nás</h2>
            <?php hb_references(link: '/o-brontosaurovi') ?>
		</div>

		<a class="hb-action hb-action--ulterior" href="/o-brontosaurovi">
			více o Hnutí Brontosaurus
		</a>
	</section>
</aside>

