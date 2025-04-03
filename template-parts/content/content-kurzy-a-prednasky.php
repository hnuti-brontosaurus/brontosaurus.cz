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

?><main class="hb-mbe-6" role="main" id="obsah">
	<h1 class="hb-ta-c">
		Vzdělávací kurzy a přednášky
	</h1>

	<h2 class="hb-sr-only">
		Seznam akcí
	</h2>

	<div class="filters hb-expandable hb-mbe-4"<?php if ($filters->isAnySelected): ?> data-hb-expandable-expanded="1"<?php endif; ?>>
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
	<p class="eventsPage__info hb-mbns-3 hb-fs-xs hb-ta-c hb-mbe-5">
		👉 Na této stránce je přehled aktuálně vypsaných kurzů. Pro více informací či další kurzy
		<a href="https://organizator.brontosaurus.cz" rel="noopener">klikni zde</a>.
	</p>
	<?php endif; ?>

    <?php hb_eventList($eventCollection) ?>
</main>


<aside>
	<section class="description hb-mbe-6">
		<h1>
			Kurzy a přednášky
		</h1>

		<p class="description__text">
			Člověk se neustále učí a Hnutí Brontosaurus se stará o to, aby měl každý dostatek prostoru pro svůj
			rozvoj. Sekce vzdělávání mimo organizátorských kurzů, kde je možné dostat certifikát
			organizátora brontosauřích akcí nebo si udělat kurs pro hlavní vedoucí dětských táborů,
			pořádá množství vzdělávacích akcí zaměřených na vše od time managementu, bylinkářství,
			grafiky, zpětné vazby a dalších. Brontosauří vzdělávání probíhá v přátelském otevřeném
			prostředí s důrazem na metody neformálního vzdělávání a praxi.
		</p>

		<div class="description__list">
			<a class="description__item optionBox button--secondary-wrapper" href="/kurzy-a-prednasky?jen=prednasky#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Přednášky
					</h2>

					<p class="optionBox__description">
						Pravidelně zveme odborníky, cestovatele, lektory i další zajímavé osobnosti.
						Přednáší u nás na témata životní prostředí, dobrovolnictví, cestování, kultura či osobnostní rozvoj.
						Přijďte si poslechnout a debatovat.
					</p>
				</div>

				<div class="optionBox__image" style="--hb-optionBox-image: url('https://brontosaurus.cz/wp-content/uploads/2024/12/meetups-scaled.jpg'); --hb-optionBox-y-offset: 20%;">
					<div class="button button--secondary">Najít přednášky</div>
				</div>
			</a>

			<a class="description__item optionBox button--secondary-wrapper" href="/kurzy-a-prednasky?jen=organizatorske-kurzy#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Organizátorské kurzy
					</h2>

					<p class="optionBox__description">
						Ne každému stačí se akcí jenom zúčastnit. Pokud
						máš chuť uspořádat brontosauří akci nebo se naučit
						mnoho užitečných dovedností pro pořádání svých
						vlastních akcí, podívej se na info.
					</p>
				</div>

				<div class="optionBox__image" style="--hb-optionBox-image: url('https://brontosaurus.cz/wp-content/uploads/2024/12/courses-organizing.jpg'); --hb-optionBox-y-offset: 41%;">
					<div class="button button--secondary">Najít organizátorský kurz</div>
				</div>
			</a>

			<a class="description__item optionBox button--secondary-wrapper" href="/kurzy-a-prednasky?jen=tematicke-kurzy#obsah">
				<div class="description__itemText optionBox__text">
					<h2 class="optionBox__heading">
						Tématické kurzy
					</h2>

					<p class="optionBox__description">
						Na brontosauřím ústředí a někdy i na zajímavých
						místech po celé republice se můžeš zúčastnit
						mnoha kurzů. Někdy se lze i něco přiučit na klubech v Praze i v Brně!
					</p>
				</div>

				<div class="optionBox__image" style="--hb-optionBox-image: url('https://brontosaurus.cz/wp-content/uploads/2024/12/courses-thematic-2.jpg'); --hb-optionBox-y-offset: 96%; --hb-optionBox-size: 144%;">
					<div class="button button--secondary">Najít tématický kurz</div>
				</div>
			</a>
		</div>
	</section>


	<section class="description hb-mbe-6">
		<h1>
			Programy pro střední školy
		</h1>

		<p class="description__text">
			Brontosaurus nezapomíná ani na středoškoláky, kteří na brontosauří akce nejezdí. Naši lektoři
			objíždějí střední školy po celé republice a baví se s žáky o dobrovolnictví, různých
			ekosystémech a o přírodě obecně.
		</p>

		<p class="description__text">
			Dvouhodinové programy probíhají v češtině nebo v angličtině. Celodenní programy se odehrávají
			v terénu, kde mimo různých aktivit čeká studenty i samotná dobrovolnická pomoc
			přírodě. Metodika programů je připravena tak, aby programy rozvíjely klíčové kompetence
			žáků a naši vyškolení lektoři používají prvky neformálního vzdělávání, díky kterým probouzí
			zájem žáků o svět kolem nás.
		</p>

		<a class="eventsPage__highSchoolsPrograms hero button--secondary-wrapper" href="/programy-pro-stredni-skoly" style="--hero-background-image-url: url('https://brontosaurus.cz/wp-content/uploads/2024/12/highSchoolPrograms-scaled.jpg')">
			<div class="button button--secondary">Nabídka programů pro školy</div>
		</a>
	</section>

    <?php hb_related(excludeCourses: true) ?>
</aside>
