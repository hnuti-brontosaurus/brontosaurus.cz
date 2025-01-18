<?php

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\DataContainers\ForChildrenFiltersDC;
use HnutiBrontosaurus\Theme\Filters\ForChildrenFilters;

/** @var Container $hb_container defined in functions.php */
$hb_bisApiClient = $hb_container->getBisClient();
$hb_dateFormatHuman = $hb_container->getDateFormatForHuman();
$hb_dateFormatRobot = $hb_container->getDateFormatForRobot();

// todo: use some WP way of obtaining param
$selectedFilter = filter_input( INPUT_GET, 'jen' ) ?? null;
$selectedFilter = $selectedFilter !== null && $selectedFilter !== '' ? htmlspecialchars($selectedFilter) : null;

$params = new EventParameters();
ForChildrenFilters::apply($selectedFilter, $params);

try {
    $events = $hb_bisApiClient->getEvents($params);
    $eventCollection = new EventCollectionDC($events, $hb_dateFormatHuman, $hb_dateFormatRobot);

} catch (ConnectionToBisFailed) {
    $eventCollection = EventCollectionDC::unableToLoad($hb_dateFormatHuman, $hb_dateFormatRobot);
}

$filters = ForChildrenFiltersDC::from('jen', $selectedFilter);

?><main class="hb-events hb-mbe-6" role="main" id="obsah">
	<h1 class="hb-events__heading">
		Akce pro děti i rodiče
	</h1>

	<div class="hb-ta-c hb-mbe-4">
		<a class="eventsPage__map" href="/o-brontosaurovi/hnuti-brontosaurus#mapa-detske-oddily">
			BRĎO – Brontosauří dětské oddíly
		</a>
	</div>

	<h2 class="screenreaders-only">
		Akce pro děti a rodiče s dětmi
	</h2>

	<div class="hb-events__filters filters hb-expandable"<?php if ($filters->isAnySelected): ?> data-hb-expandable-expanded="1"<?php endif; ?>>
		<button class="hb-expandable__toggler button button--customization" type="button" aria-hidden="true" data-hb-expandable-toggler>
			Zobrazit pouze
		</button>

		<ul class="filters__list" data-hb-expandable-content>
			<li class="filters__item">
				<a class="filters__link<?php if ( ! $filters->isAnySelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/pro-deti#obsah">
					vše
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isCampsSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/pro-deti?jen=detske-tabory#obsah">
					dětské tábory
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isUnitsSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/pro-deti?jen=detske-oddily#obsah">
					dětské oddíly
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isEventsSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/pro-deti?jen=akce-pro-deti#obsah">
					akce pro děti
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isEventsWithParentsSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/pro-deti?jen=akce-pro-rodice-s-detmi#obsah">
					akce pro rodiče s dětmi
				</a>
			</li>
		</ul>
	</div>

    <?php hb_eventList($eventCollection) ?>
</main>

<aside>
	<section class="description hb-mbe-5">
		<h1>
			Programy pro děti
		</h1>

		<p class="description__text">
			Brontosaurus nezapomíná ani na naše nejmenší. Brontosauří dětské oddíly BRĎO jsou pravidelně se setkávající kluby,
			které pořádají i dětské tábory a další akce. Díky BRĎU mohou i nejmenší brontosauři pomoci přírodě a zároveň se naučit zajímat o svět kolem sebe.
			Oddíly Brďo vedou zkušení organizátoři proškolení naším interním kurzem organizátora BRĎO.
		</p>

		<p class="description__text">
			Hnutí Brontosaurus je jednou ze 17 organizací, které se mohou pyšnit titulem Organizace uznaná pro práci
			s dětmi a mládeží, který uděluje Ministerstvo školství, mládeže a tělovýchovy ČR.
		</p>

		<div class="description__list">
			<a class="description__item optionBox button--secondary-wrapper" href="/pro-deti?jen=detske-tabory#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Tábory pro děti
					</h2>

					<p class="optionBox__description">
						Brontosauří tábory pro děti se vyznačují silným příběhem,
						rozmanitostí aktivit a výchovou dětí k přírodě a pečování o ni.
					</p>
				</div>

				<div class="optionBox__image optionBox__image--forChildren-camps">
					<div class="button button--secondary">Najít tábor</div>
				</div>
			</a>

			<a class="description__item optionBox button--secondary-wrapper" href="/pro-deti?jen=detske-oddily#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Dětské oddíly
					</h2>

					<p class="optionBox__description">
						Brontosauří dětské oddíly se pravidelně scházejí v klubovně
						i na výletech do přírody. Oddíly vedou proškolení organizátoři.
					</p>
				</div>

				<div class="optionBox__image optionBox__image--forChildren-units">
					<div class="button button--secondary">Seznam dětských oddílů</div>
				</div>
			</a>

			<a class="description__item optionBox button--secondary-wrapper" href="/pro-deti?jen=akce-pro-deti#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Akce pro děti
					</h2>

					<p class="optionBox__description">
						Hnutí Brontosaurus nezapomíná ani na ty nejmenší, a proto pořádáme dobrodružné výpravy a pobyty v přírodě,
						nejrůznější akce pro veřejnost a samozřejmě také letní tábory.
						Díky spojení zážitků s ekologickou výchovou se nám
						daří probouzet v dětech zájem o přírodu a její ochranu.
					</p>
				</div>

				<div class="optionBox__image optionBox__image--forChildren-events">
					<div class="button button--secondary">Najít akci pro děti</div>
				</div>
			</a>

			<a class="description__item optionBox button--secondary-wrapper" href="/pro-deti?jen=akce-pro-rodice-s-detmi#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Akce pro rodiče s dětmi
					</h2>

					<p class="optionBox__description">
						Pro aktivní rodiče, kteří nechtějí strávit dovolenou
						s dětmi vyvalováním se u moře, pořádá Brontosaurus tábory
						pro rodiče s dětmi, kde se zážitkový program a pohoda střídá s dobrovolnickou pomocí.
					</p>
				</div>

				<div class="optionBox__image optionBox__image--forChildren-eventsWithParents">
					<div class="button button--secondary">Najít akci pro rodiče s dětmi</div>
				</div>
			</a>
		</div>
	</section>

	<?php hb_related(excludeForChildren: true) ?>
</aside>
