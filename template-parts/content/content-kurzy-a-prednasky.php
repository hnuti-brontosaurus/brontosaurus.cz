<?php

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\DataContainers\CoursesFiltersDC;
use HnutiBrontosaurus\Theme\Filters\CoursesFilters;

/** @var Container $hb_container defined in functions.php */
$hb_bisApiClient = $hb_container->getBisClient();
$hb_dateFormatHuman = $hb_container->getDateFormatForHuman();
$hb_dateFormatRobot = $hb_container->getDateFormatForRobot();

// todo: use some WP way of obtaining param
$selectedFilter = filter_input( INPUT_GET, 'jen' ) ?? null;
$selectedFilter = $selectedFilter !== null && $selectedFilter !== '' ? htmlspecialchars($selectedFilter) : null;

$params = new EventParameters();
CoursesFilters::apply($selectedFilter, $params);

try {
    $events = $hb_bisApiClient->getEvents($params);
    $eventCollection = new EventCollectionDC($events, $hb_dateFormatHuman, $hb_dateFormatRobot);

} catch (ConnectionToBisFailed) {
    $eventCollection = EventCollectionDC::unableToLoad($hb_dateFormatHuman, $hb_dateFormatRobot);
}

$filters = CoursesFiltersDC::from('jen', $selectedFilter);

?><main class="hb-events hb-mbe-6" role="main" id="obsah">
	<h1 class="hb-events__heading">
		Vzdělávací kurzy a&nbsp;přednášky
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
				<a class="filters__link<?php if ( ! $filters->isAnySelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/kurzy-a-prednasky#obsah">
					vše
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isMeetupsSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/kurzy-a-prednasky?jen=prednasky#obsah">
					přednášky
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isOrganizingSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/kurzy-a-prednasky?jen=organizatorske-kurzy#obsah">
					organizátorské kurzy
				</a>
			</li>

			<li class="filters__item">
				<a class="filters__link<?php if ($filters->isThematicSelected): ?> filters__link--selected<?php endif; ?> button button--customization" href="/kurzy-a-prednasky?jen=tematicke-kurzy#obsah">
					tématické kurzy
				</a>
			</li>
		</ul>
	</div>

	<?php if ($filters->isOrganizingSelected): ?>
	<p class="eventsPage__info hb-mbns-3 hb-ta-c hb-mbe-5">
		👉 Na této stránce je přehled aktuálně vypsaných kurzů. Pro více informací či další kurzy
		<a href="https://organizator.brontosaurus.cz" rel="noopener">klikni zde</a>.
	</p>
	<?php endif; ?>

    <?php hb_eventList($eventCollection) ?>
</main>


<aside>
	<section class="description hb-mbe-6">
		<h1>
			Kurzy a&nbsp;přednášky
		</h1>

		<p class="description__text">
			Člověk&nbsp;se neustále učí a&nbsp;Hnutí Brontosaurus se&nbsp;stará o&nbsp;to, aby&nbsp;měl každý dostatek prostoru pro&nbsp;svůj
			rozvoj. Sekce vzdělávání mimo organizátorských kurzů, kde je&nbsp;možné dostat certifikát
			organizátora brontosauřích akcí nebo si&nbsp;udělat kurs pro&nbsp;hlavní vedoucí dětských táborů,
			pořádá množství vzdělávacích akcí zaměřených na&nbsp;vše od&nbsp;time managementu, bylinkářství,
			grafiky, zpětné vazby a&nbsp;dalších. Brontosauří vzdělávání probíhá v&nbsp;přátelském otevřeném
			prostředí s&nbsp;důrazem na&nbsp;metody neformálního vzdělávání a&nbsp;praxi.
		</p>

		<div class="description__list">
			<a class="description__item optionBox button--secondary-wrapper" href="/kurzy-a-prednasky?jen=prednasky#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Přednášky
					</h2>

					<p class="optionBox__description">
						Pravidelně zveme odborníky, cestovatele, lektory i&nbsp;další zajímavé osobnosti.
						Přednáší u&nbsp;nás na&nbsp;témata životní prostředí, dobrovolnictví, cestování, kultura či&nbsp;osobnostní rozvoj.
						Přijďte si&nbsp;poslechnout a&nbsp;debatovat.
					</p>
				</div>

				<div class="optionBox__image optionBox__image--meetups">
					<div class="button button--secondary">Najít přednášky</div>
				</div>
			</a>

			<a class="description__item optionBox button--secondary-wrapper" href="/kurzy-a-prednasky?jen=organizatorske-kurzy#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Organizátorské kurzy
					</h2>

					<p class="optionBox__description">
						Ne&nbsp;každému stačí se&nbsp;akcí jenom zúčastnit. Pokud
						máš chuť uspořádat brontosauří akci nebo se&nbsp;naučit
						mnoho užitečných dovedností pro&nbsp;pořádání svých
						vlastních akcí, podívej&nbsp;se na&nbsp;info.
					</p>
				</div>

				<div class="optionBox__image optionBox__image--courses-organizing">
					<div class="button button--secondary">Najít organizátorský kurz</div>
				</div>
			</a>

			<a class="description__item optionBox button--secondary-wrapper" href="/kurzy-a-prednasky?jen=tematicke-kurzy#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Tématické kurzy
					</h2>

					<p class="optionBox__description">
						Na&nbsp;brontosauřím ústředí a&nbsp;někdy i&nbsp;na&nbsp;zajímavých
						místech po&nbsp;celé republice se&nbsp;můžeš zúčastnit
						mnoha kurzů. Někdy se&nbsp;lze i&nbsp;něco přiučit na&nbsp;klubech v&nbsp;Praze i&nbsp;v&nbsp;Brně!
					</p>
				</div>

				<div class="optionBox__image optionBox__image--courses-thematic">
					<div class="button button--secondary">Najít tématický kurz</div>
				</div>
			</a>
		</div>
	</section>


	<section class="description hb-mbe-6">
		<h1>
			Programy pro&nbsp;střední školy
		</h1>

		<p class="description__text">
			Brontosaurus nezapomíná ani na&nbsp;středoškoláky, kteří na&nbsp;brontosauří akce nejezdí. Naši lektoři
			objíždějí střední školy po&nbsp;celé republice a&nbsp;baví&nbsp;se s&nbsp;žáky o&nbsp;dobrovolnictví, různých
			ekosystémech a&nbsp;o&nbsp;přírodě obecně.
		</p>

		<p class="description__text">
			Dvouhodinové programy probíhají v&nbsp;češtině nebo&nbsp;v&nbsp;angličtině. Celodenní programy se&nbsp;odehrávají
			v&nbsp;terénu, kde&nbsp;mimo různých aktivit čeká studenty i&nbsp;samotná dobrovolnická pomoc
			přírodě. Metodika programů je připravena tak, aby&nbsp;programy rozvíjely klíčové kompetence
			žáků a&nbsp;naši vyškolení lektoři používají prvky neformálního vzdělávání, díky kterým probouzí
			zájem žáků o&nbsp;svět kolem&nbsp;nás.
		</p>

		<a class="eventsPage__highSchoolsPrograms hero button--secondary-wrapper" href="/programy-pro-stredni-skoly" style="--hero-background-image-url: url('https://brontosaurus.cz/wp-content/uploads/2024/12/highSchoolPrograms-scaled.jpg')">
			<div class="button button--secondary">Nabídka programů pro&nbsp;školy</div>
		</a>
	</section>

    <?php hb_related(excludeCourses: true) ?>
</aside>
