<?php

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventDC;
use HnutiBrontosaurus\Theme\DataContainers\VoluntaryFiltersDC;
use HnutiBrontosaurus\Theme\Filters\VoluntaryFilters;

/** @var Container $hb_container defined in functions.php */
$hb_bisApiClient = $hb_container->getBisClient();
$hb_dateFormatHuman = $hb_container->getDateFormatForHuman();
$hb_dateFormatRobot = $hb_container->getDateFormatForRobot();

/**
 * @param Event[] $events
 * @return Event[]
 */
function hb_postFilter(array $events, string $selectedFilter): array
{
    $filteredEvents = [];

    foreach ($events as $event) {
        if ($selectedFilter === VoluntaryFilters::FILTER_ONE_DAY_EVENTS && $event->getDuration() === 1) {
            $filteredEvents[] = $event;
        }
    }

    return $filteredEvents;
}

// todo: use some WP way of obtaining param
$selectedFilter = filter_input( INPUT_GET, 'jen' ) ?? null;
$selectedFilter = $selectedFilter !== null && $selectedFilter !== '' ? htmlspecialchars($selectedFilter) : null;

$params = new EventParameters();
VoluntaryFilters::apply($selectedFilter, $params);

$eventCollection = null;
$events = [];
try {
    $events = $hb_bisApiClient->getEvents($params);

} catch (ConnectionToBisFailed) {
    $eventCollection = EventCollectionDC::unableToLoad($hb_dateFormatHuman, $hb_dateFormatRobot);
}

if ($eventCollection === null) {
    // in case of one-day events, we need to do post-filtering because BIS doesn't distinct on domain level
    if ($selectedFilter === VoluntaryFilters::FILTER_ONE_DAY_EVENTS) {
        $events = hb_postFilter($events, $selectedFilter);
    }

    $eventCollection = new EventCollectionDC($events, $hb_dateFormatHuman, $hb_dateFormatRobot);
}

$filters = VoluntaryFiltersDC::from('jen', $selectedFilter);

?><main class="hb-events hb-mbe-6" role="main" id="obsah">
	<h1 class="hb-events__heading">
		Zážitkové a&nbsp;dobrovolnické akce
	</h1>

	<h2 class="screenreaders-only">
		Seznam akcí
	</h2>

	<div class="hb-events__filters filters hb-expandable"<?php if ($filters->isAnySelected): ?> data-hb-expandable-expanded="1"<?php endif; ?>>
		<button class="hb-expandable__toggler button button--customization" type="button" aria-hidden="true" data-hb-expandable-toggler>
			Zobrazit pouze
		</button>

		<ul class="filters__list" data-hb-expandable-content>
			<li class="filters__item">
				<a class="filters__link<?php if ( ! $filters->isAnySelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/dobrovolnicke-akce#obsah">
					vše
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isFirstTimeAttendeesSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/dobrovolnicke-akce?jen=jedu-poprve#obsah">
					jedu poprvé
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isWeekendEventsSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/dobrovolnicke-akce?jen=vikendovky#obsah">
					víkendovky
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isOneDayEventsSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/dobrovolnicke-akce?jen=jednodenni#obsah">
					jednodenní
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isHolidayEventsSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/dobrovolnicke-akce?jen=prazdninove#obsah">
					prázdninové
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isNatureSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/dobrovolnicke-akce?jen=priroda#obsah">
					příroda
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isSightsSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/dobrovolnicke-akce?jen=pamatky#obsah">
					památky
				</a>
			</li>
		</ul>
	</div>

    <?php hb_eventList($eventCollection) ?>
</main>


<aside>
	<section class="description hb-mbe-5">
		<h1 id="co-zazijes-na-dobrovolnicke-akci">
			Co&nbsp;zažiješ na&nbsp;dobrovolnické akci
		</h1>

		<p class="description__text">
			Na&nbsp;každé brontosauří dobrovolnické akci tě mimo samotné pomoci přírodě nebo památkám
			čeká i&nbsp;zážitkový program. Jsou&nbsp;to různé hry nebo&nbsp;workshopy, které připravujeme tak, že&nbsp;se
			na&nbsp;naší akci něco&nbsp;naučíš, získáš příležitost k&nbsp;seberozvoji a&nbsp;k&nbsp;poznání nových kamarádů.
			Dobrovolnictví a&nbsp;zážitky jsou pro&nbsp;nás cestou k&nbsp;osobnímu růstu a&nbsp;objevování nového světa
			kolem&nbsp;nás.

		</p>

		<div class="description__list">
			<a class="description__item description__item--fullWidth optionBox button--secondary-wrapper" href="/dobrovolnicke-akce?jen=jedu-poprve#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Jedu poprvé
					</h2>

					<p class="optionBox__description">
						Podívej&nbsp;se na&nbsp;nabídku akcí zvláště vhodných pro&nbsp;ty, co&nbsp;s&nbsp;námi chtějí jet poprvé. Akce se&nbsp;konají
						v&nbsp;přírodních lokalitách nebo na&nbsp;hradech a&nbsp;zámcích. Bývají na&nbsp;nich převážně mladí lidé ve&nbsp;věku 15&ndash;22&nbsp;let.
						Na&nbsp;akci je zajištěn program, součástí je vždy dobrovolnická pomoc dané lokalitě. Zabezpečeno je po&nbsp;celou dobu také
						kvalitní jídlo a&nbsp;ubytování ve&nbsp;spacácích.
					</p>
				</div>

				<div class="optionBox__image optionBox__image--voluntary-firstTime">
					<div class="button button--secondary">Najít akci</div>
				</div>
			</a>

			<a class="description__item--fullWidth hb-js-c action action--ulterior" href="/jedu-poprve">
				Co čekat, když jedu poprvé?
			</a>

			<a class="description__item optionBox button--secondary-wrapper" href="/dobrovolnicke-akce?jen=vikendovky#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Víkendovky
					</h2>

					<p class="optionBox__description">
						Víkendové akce typicky probíhají na&nbsp;jaře a&nbsp;na&nbsp;podzim. Ať&nbsp;jde o&nbsp;kosení orchidejových luk, věšení
						a&nbsp;údržba ptačích budek nebo&nbsp;opravu hradní zdi,
						čeká&nbsp;tě spousta zábavy, nových přátelství a&nbsp;zážitků
						na&nbsp;celý život.
					</p>
				</div>

				<div class="optionBox__image optionBox__image--voluntary-weekendEvents">
                    <div class="button button--secondary">Najít akci</div>
				</div>
			</a>

			<a class="description__item optionBox button--secondary-wrapper" href="/dobrovolnicke-akce?jen=jednodenni#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Jednodenní akce
					</h2>

					<p class="optionBox__description">
						Ne&nbsp;všechny brontosauří akce trvají více dní.
						Občas pořádáme jednodenní akce, při&nbsp;kterých
						třeba věšíme ptačí budky, potkáváme se po&nbsp;úspěšné
						sezóně, máme různé workshopy nebo&nbsp;kluby
						a&nbsp;kurzy. Podívej&nbsp;se, co&nbsp;všechno s&nbsp;námi
						můžeš během jednoho dne zažít!
					</p>
				</div>

				<div class="optionBox__image optionBox__image--voluntary-oneDayEvents">
                    <div class="button button--secondary">Najít akci</div>
				</div>
			</a>

			<a class="description__item description__item--fullWidth optionBox button--secondary-wrapper" href="/dobrovolnicke-akce?jen=prazdninove#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Prázdninové akce
					</h2>

					<p class="optionBox__description">
						Prázdninové akce se&nbsp;nejčastěji konají během
						letních prázdnin a&nbsp;jsou&nbsp;to vlastně takové tábory
						pro&nbsp;mladé dospěláky spojené s&nbsp;dobrovolnickou
						pomocí. Většinou mají ucelený příběh, který
						rámuje zážitkový program a&nbsp;většina dní
						obsahuje i&nbsp;dobrovolnickou pomoc.
					</p>
				</div>

				<div class="optionBox__image optionBox__image--voluntary-holidayEvents">
                    <div class="button button--secondary">Najít akci</div>
				</div>
			</a>

			<a class="description__item optionBox button--secondary-wrapper" href="/dobrovolnicke-akce?jen=priroda#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Příroda
					</h2>

					<p class="optionBox__description">
						Pojď s&nbsp;námi pomáhat vzácným rostlinám i&nbsp;živočichům,
						podívat&nbsp;se na&nbsp;nádherná místa po&nbsp;celé
						republice, pohladit&nbsp;si lišku na&nbsp;záchranné stanici,
						rozpomenout&nbsp;se, jak&nbsp;voní suchá tráva, obdivovat
						krásu orchidejí a&nbsp;poslouchat zpěv ptáků, kterým
						jsme zrovna vyvěsili ptačí budky.
					</p>
				</div>

				<div class="optionBox__image optionBox__image--voluntary-nature">
                    <div class="button button--secondary">Akce pomáhající přírodě</div>
				</div>
			</a>

			<a class="description__item optionBox button--secondary-wrapper" href="/dobrovolnicke-akce?jen=pamatky#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Památky
					</h2>

					<p class="optionBox__description">
						Cítíš&nbsp;se doma na&nbsp;starobylých hradech nebo v&nbsp;pokoji
						vily z&nbsp;první republiky? Podívej&nbsp;se na&nbsp;místa,
						která jsou normálním návštěvníkům zapovězena a&nbsp;vyzkoušej&nbsp;si,
						jak se&nbsp;staví hradby, udržují jedinečné
						zahrady nebo jak se&nbsp;tancuje na&nbsp;místech, kde se&nbsp;dříve odehrávaly středověké
						plesy.
					</p>
				</div>

				<div class="optionBox__image optionBox__image--voluntary-sights">
                    <div class="button button--secondary">Akce pomáhající památkám</div>
				</div>
			</a>
		</div>
	</section>

    <?php hb_related(excludeVoluntary: true) ?>
</aside>
