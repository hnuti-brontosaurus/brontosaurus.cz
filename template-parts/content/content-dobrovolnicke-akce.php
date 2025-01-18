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
		Zážitkové a dobrovolnické akce
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
			Co zažiješ na dobrovolnické akci
		</h1>

		<p class="description__text">
			Na každé brontosauří dobrovolnické akci tě mimo samotné pomoci přírodě nebo památkám
			čeká i zážitkový program. Jsou to různé hry nebo workshopy, které připravujeme tak, že se
			na naší akci něco naučíš, získáš příležitost k seberozvoji a k poznání nových kamarádů.
			Dobrovolnictví a zážitky jsou pro nás cestou k osobnímu růstu a objevování nového světa
			kolem nás.

		</p>

		<div class="description__list">
			<a class="description__item description__item--fullWidth optionBox button--secondary-wrapper" href="/dobrovolnicke-akce?jen=jedu-poprve#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Jedu poprvé
					</h2>

					<p class="optionBox__description">
						Podívej se na nabídku akcí zvláště vhodných pro ty, co s námi chtějí jet poprvé. Akce se konají
						v přírodních lokalitách nebo na hradech a zámcích. Bývají na nich převážně mladí lidé ve věku 15&ndash;22 let.
						Na akci je zajištěn program, součástí je vždy dobrovolnická pomoc dané lokalitě. Zabezpečeno je po celou dobu také
						kvalitní jídlo a ubytování ve spacácích.
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
						Víkendové akce typicky probíhají na jaře a na podzim. Ať jde o kosení orchidejových luk, věšení
						a údržba ptačích budek nebo opravu hradní zdi,
						čeká tě spousta zábavy, nových přátelství a zážitků
						na celý život.
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
						Ne všechny brontosauří akce trvají více dní.
						Občas pořádáme jednodenní akce, při kterých
						třeba věšíme ptačí budky, potkáváme se po úspěšné
						sezóně, máme různé workshopy nebo kluby
						a kurzy. Podívej se, co všechno s námi
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
						Prázdninové akce se nejčastěji konají během
						letních prázdnin a jsou to vlastně takové tábory
						pro mladé dospěláky spojené s dobrovolnickou
						pomocí. Většinou mají ucelený příběh, který
						rámuje zážitkový program a většina dní
						obsahuje i dobrovolnickou pomoc.
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
						Pojď s námi pomáhat vzácným rostlinám i živočichům,
						podívat se na nádherná místa po celé
						republice, pohladit si lišku na záchranné stanici,
						rozpomenout se, jak voní suchá tráva, obdivovat
						krásu orchidejí a poslouchat zpěv ptáků, kterým
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
						Cítíš se doma na starobylých hradech nebo v pokoji
						vily z první republiky? Podívej se na místa,
						která jsou normálním návštěvníkům zapovězena a vyzkoušej si,
						jak se staví hradby, udržují jedinečné
						zahrady nebo jak se tancuje na místech, kde se dříve odehrávaly středověké
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
