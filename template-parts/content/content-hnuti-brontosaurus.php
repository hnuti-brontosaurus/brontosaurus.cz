<?php

use HnutiBrontosaurus\Theme\CannotResolveCoordinates;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\DataContainers\Structure\AdministrationUnit;
use Tracy\Debugger;
use Tracy\ILogger;

/** @var Container $hb_container defined in functions.php */

$hb_bisApiClient = $hb_container->getBisClient();
$hb_coordinatesResolver = $hb_container->getCoordinatesResolver();

$administrationUnits = [];
$hasBeenUnableToLoad = false;

try {
    foreach ($hb_bisApiClient->getAdministrationUnits() as $administrationUnit) {
        try {
            $administrationUnits[] = AdministrationUnit::from($administrationUnit, $hb_coordinatesResolver);

        } catch (CannotResolveCoordinates $e) {
            Debugger::log($e, ILogger::WARNING);
            continue; // if can not resolve (e.g. non-existing address) just ignore this unit and continue
        }
    }

} catch (ConnectionToBisFailed) {
    $hasBeenUnableToLoad = true;
}

$administrationUnitsInJson = json_encode($administrationUnits);

?><main role="main">
	<article>
		<h1>
			Hnutí Brontosaurus
		</h1>

		<div class="hb-lg-mw-40 [ hb-d-f hb-lg-jc-c hb-ai-c ][ hb-lg-mi-auto hb-mbe-5 ]">
			<div>
				<p class="hb-block-text hb-mbe-4">
					Brontosaurus pomáhá přírodě, památkám i lidem po celé republice i v zahraničí. Aby vše
					lépe fungovalo, máme po celé republice regionální centra, základní články a kluby, které
					pořádají víkendovky, prázdninové akce, dětské tábory, přednášky a mnoho dalšího.
				</p>

				<p class="hb-block-text">
					V České republice máme přes 40 aktivních základních článků, které organizují
					brontosauří akce. Pokud se chceš jakkoli zapojit nebo se jen zeptat, jak funguje
					Brontosaurus ve tvém regionu, podívej se na naši mapku a najdi svůj nejbližší
					základní článek.
				</p>
			</div>

			<div class="about__graphics hb-brontographics">
				<svg
					fill-rule="evenodd" viewBox="0 0 600 699.999" role="list" aria-labelledby="about-structure-image-title"
					xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
				>
					<desc id="about-structure-image-title">Struktura organizace</desc>

					<a xlink:href="#zakladni-clanky">
						<path class="hb-brontographics__shape" role="listitem" aria-labelledby="about-structure-image-base" d="M539.302 525.998l0 4.834c-1.444,14.12 -13.366,41.742 -26.574,54.95l-5.445 4.953c-1.841,1.85 -3.516,3.167 -5.643,4.755 -18.042,11.699 -24.321,10.842 -43.307,11.112 -1.286,0.024 -1.532,-0.833 -1.223,-1.825 2.461,-7.811 3.985,-12.78 6.469,-20.804 0.524,-1.842 0.294,-3.318 -1.484,-4.35l-66.357 -25.773c-0.92,-0.405 -1.73,-0.984 -2.579,-2.151 -9.597,-13.216 -28.266,-47.529 -42.783,-71.596 -0.341,-0.722 -0.54,-1.23 -0.818,-2.397l-15.089 -82.358c-0.325,-1.604 -1.286,-2.62 -2.58,-3.493 -9.048,-6.159 -16.867,-11.533 -26.098,-17.882 -2.199,-1.651 -3.667,-1.643 -6.35,0.508l-31.654 25.653c-0.468,0.389 -0.976,0.739 -1.516,1.048 -16.058,8.517 -28.051,15.255 -44.108,23.772 -4.192,2.802 -5.89,1.056 -5.033,-3.238 1.413,-7.049 2.865,-12.7 4.017,-17.574 0.348,-1.444 0.856,-2.984 1.166,-3.968l5.794 -11.605c9.716,-18.28 23.844,-37.322 39.632,-51.077 9.89,-8.62 23.907,-17.113 39.584,-17.113 17.105,0 32.575,7.699 44.06,19.24 5.223,5.255 9.724,9.969 14.256,15.986 7.549,10.001 14.51,21.026 20.114,32.329l26.256 56.427c0.905,2.183 1.477,3.159 2.405,5.151l11.811 25.519c1.691,3.636 2.953,6.835 4.723,10.39 0.968,1.953 1.508,3.128 2.381,5.176 4.54,10.596 11.501,25.527 18.455,33.996 2.15,2.611 4.508,5.445 7.048,7.596 2.865,2.421 5.072,4.722 8.279,6.842 10.009,6.636 21.129,11.207 34.614,8.818 13.51,-2.389 19.407,-9.437 26.241,-19.113 1.445,-2.056 4.802,-6.62 8.025,-6.62 1.889,0 2.945,1.429 3.31,3.881l0.001 0.001z" />
						<text class="hb-brontographics__label" id="about-structure-image-base" x="370.745" y="293.868">
							Základní
							<tspan dx="-2.45em" dy="1.1em">články</tspan>
						</text>
					</a>

					<a xlink:href="#kluby">
						<path class="hb-brontographics__shape" role="listitem" aria-labelledby="about-structure-image-clubs" d="M216.353 566.319l-20.804 6.66c-0.54,0.198 -1.135,0.278 -1.762,0.183l-54.768 -8.668c-2.278,-0.365 -7.025,-1.238 -8.16,-6.715 -0.659,-3.215 -0.191,-8.834 1.119,-15.192 1.341,-5.048 3.215,-9.946 5.406,-15.621 1.841,-5.533 3.69,-10.819 5.437,-16.764 0.897,-3.04 1.96,-5.786 2.936,-8.405 7.565,-20.17 18.487,-56.801 21.94,-77.755 3.992,-24.217 4.111,-37.616 6.588,-60.277l0.428 -4.651c0.476,-3.477 0.095,-3.263 3.112,-3.866l30.686 -6.421c4.135,-0.635 4.214,-0.508 4.365,3.175l1.008 18.97c1.128,9.866 2.271,28.154 2.04,29.535l-0.183 1.239 -22.574 117.41c-0.079,1.444 -0.507,2.381 0.786,4.484l22.765 39.775c1.008,1.746 0.643,2.595 -0.365,2.905l0 -0.001z" />
						<text class="hb-brontographics__label" id="about-structure-image-clubs" x="77.026" y="429.509">Kluby</text>
					</a>

					<a xlink:href="#regionalni-centra">
						<path class="hb-brontographics__shape" role="listitem" aria-labelledby="about-structure-image-regional" d="M452.309 601.927c-0.516,1.278 -1.033,2.548 -1.541,3.826 -0.079,0.19 -0.111,0.452 -0.111,0.746 0.199,2.413 1.254,4.747 1.81,6.723 0.73,2.595 1.095,6.215 0.817,7.032 -0.357,0.969 -2.531,1.064 -4.698,1.43 -4.93,0.833 -9.446,0.801 -14.415,1.539l-10.62 0.77c-7.35,0.064 -14.621,0.905 -21.844,0.913l-32.257 1.413c-3.35,0.278 -7.883,0.024 -11.359,0.024l-10.906 0.476c-5.072,0.278 -11.835,0.318 -16.732,-0.341 -1.73,-0.238 -3.318,-0.016 -5.024,-0.191 -13.883,-1.404 -19.019,-10.921 -21.876,-23.177l-10.906 0c0.738,1.095 1.223,2.048 1.985,3.223 0.761,1.174 1.174,1.706 1.817,2.928 4.215,8.065 2.199,15.153 -6.374,17.368 -5.119,1.317 -11.056,1.143 -17.351,1.143 -6.635,0 -11.541,0.467 -18.018,0.467 -6.009,0 -12.017,0.008 -18.026,0 -6.08,-0.007 -11.453,0.477 -18.018,0.477 -5.993,0 -12.057,-0.095 -18.042,-0.016l-18.002 0.492c-12.882,0 -34.916,2.35 -43.155,-9.961 -2.366,-0.254 -3.683,-0.413 -6.152,-0.485 -14.509,-0.436 -29.138,-0.063 -43.648,0.016 -10.35,0.056 -21.788,-2.571 -26.416,-11.057 -4.46,-8.167 -2.555,-20.835 1.771,-28.296 10.334,-17.804 42.195,-21.114 61.411,-13.161 0.619,0.254 1.191,0.588 1.778,0.874 0.58,0.285 1.778,0.825 2.286,0.968 0.564,0.159 3.159,0.42 3.397,0.444 55.228,9.009 107.663,17.494 162.891,26.511 2.017,0.239 3.525,0.024 4.874,-1.127 4.644,-4.659 9.295,-9.318 13.946,-13.978 0.913,-0.809 1.445,-0.944 2.365,-1.246 24.662,-6.961 48.728,-13.771 73.39,-20.733 0.73,-0.206 1.627,-0.166 2.667,0.167l63.499 24.662c1.087,0.524 1.397,1.19 0.897,2.429l-6.112 16.708 0.002 0z" />
						<text class="hb-brontographics__label" id="about-structure-image-regional" x="272.348" y="674.216">Regionální centra</text>
					</a>

					<a xlink:href="#ustredi">
						<path class="hb-brontographics__shape" role="listitem" aria-labelledby="about-structure-image-office" d="M208.519 89.678c-0.254,-0.04 -0.603,-0.365 -0.73,-0.691 -0.889,-2.27 -1.048,-3.627 0.119,-6.294 0.42,-0.833 2.413,-3.382 8.008,-4.263 0.485,2.405 3.374,10.47 -0.142,9.954 -1.5,-0.222 -1.326,-1.103 -2.715,-1.778 -0.079,-0.04 -0.151,-0.071 -0.23,-0.087 -0.159,-0.032 -0.389,0.079 -0.508,0.143 -2.691,1.381 -2.826,3.159 -3.802,3.016zm-33.742 258.085l2.484 -47.839c-0.04,-10.818 0.953,-21.891 0.945,-32.599l0 -50.085c0,-17.764 0.516,-43.799 -8.215,-58.396 -9.485,-15.858 -25.44,-7.334 -44.839,-21.78 -21.978,-16.367 -22.161,-42.369 2.628,-52.934 11.699,-4.985 25.868,-5.08 38.266,-2.604 4.857,0.968 8.08,2.215 12.16,4.374 0,-5.422 2.5,-9.573 5.66,-11.827 2.325,-1.667 12.929,-5.438 14.374,-2.127 1.071,2.444 2.508,3.754 3.12,6.389 3.214,-2.151 5.342,-7.088 10.398,-7.088 7.501,0 10.39,7.342 10.39,14.176 8.429,0 10.533,2.088 14.025,9.128 9.462,19.074 1.802,30.702 -7.905,47.545 -13.827,23.979 -17.471,51.197 -17.463,78.454 0.016,11.842 -0.476,23.566 -0.54,35.425 -0.135,23.788 -0.27,48.537 1.016,72.301l0.644 11.129 0.039 0.841c0.151,1.484 -0.913,1.651 -1.786,1.817l-34.131 6.716c-0.142,0.023 -0.301,0.047 -0.491,0.039 -0.358,-0.007 -0.818,-0.143 -0.778,-1.055l-0.001 0zm12.803 -258.998c-0.334,-0.008 -0.572,-0.095 -0.762,-0.484 -1.81,-3.691 2.191,-10.716 8.834,-10.85 0.556,2.254 0.976,3.73 1.048,5.564 0.032,0.777 0.008,1.611 -0.095,2.603 -0.048,0.437 -0.016,1.318 -0.183,1.485 -0.016,0.015 -0.04,0.031 -0.056,0.039 -0.293,0.103 -0.539,-0.19 -0.682,-0.317 -0.421,-0.381 -0.706,-0.603 -1.294,-1.008 -0.73,-0.453 -2.397,-1.032 -2.976,-0.699 -0.064,0.024 -0.127,0.088 -0.199,0.167 -1.23,1.397 -1.452,3.603 -3.635,3.501l0 -0.001z" />
						<text class="hb-brontographics__label" id="about-structure-image-office" x="245.611" y="194.479">Ústředí</text>
					</a>

					<a xlink:href="#detske-oddily">
						<path class="hb-brontographics__shape" role="listitem" aria-labelledby="about-structure-image-children" d="M204.899 574.051c5.445,-1.921 12.446,-3.778 18.304,-5.668 0.658,-0.174 1.381,-1.008 0.571,-2.19l-23.812 -43.926c-0.865,-1.548 -0.77,-2.294 -0.373,-4.31 1.27,-6.429 2.556,-11.565 3.937,-17.978 0.191,-0.453 0.429,-0.587 1.024,-0.532 18.463,4.731 35.298,8.128 53.76,12.859 2.366,0.428 4.437,1.261 8.017,0.436l77.564 -27.678c1.334,-0.309 2.159,0.048 2.906,0.849 13.247,21.987 26.487,43.974 39.726,65.96 0.786,1.27 0.278,2.033 -0.801,2.381l-71.731 20.043c-0.746,0.23 -1.23,0.595 -1.738,1.103l-12.231 12.938c-1.501,1.214 -3.151,1.754 -6.795,1.27l-88.526 -14.105 -0.508 -0.103c-0.365,-0.127 -0.96,-0.762 0.707,-1.349l-0.001 0z" />
						<text class="hb-brontographics__label" id="about-structure-image-children" x="482.47" y="440">
							Dětské
							<tspan dx="-2.25em" dy="1.1em">oddíly</tspan>
							<tspan dx="-2em" dy="1.15em">BRĎO</tspan>
						</text>
					</a>

					<a xlink:href="#dobrovolnici">
						<path class="hb-brontographics__shape" role="listitem" aria-labelledby="about-structure-image-volunteers" d="M204.899 491.208c3.675,-18.383 6.969,-35.655 10.565,-54.046 0.421,-1.698 1.056,-3.405 2.366,-4.286l51.212 -26.225c0.754,-0.453 1.492,-1.04 2.182,-1.612 10.152,-8.247 19.852,-16.247 30.059,-24.55 0.62,-0.564 1.834,-0.802 2.898,-0.04 7.81,5.516 15.62,11.025 23.431,16.533 1.174,0.786 1.682,1.58 1.976,2.588l13.684 76.62c0.199,1.476 0.318,2.215 -1.627,3.095l-74.937 26.956c-3.08,1.199 -5.199,1.056 -7.755,0.405l-52.942 -13.034c-1.159,-0.246 -1.469,-0.635 -1.112,-2.405l0 0.001z" />
						<text class="hb-brontographics__label" id="about-structure-image-volunteers" x="436.043" y="389.852">Dobrovolníci</text>
					</a>
				</svg>
			</div>
		</div>

		<div class="hb-mw-40 hb-mi-auto hb-mbe-5">
			<?php hb_administrative_units_map($administrationUnitsInJson, $hasBeenUnableToLoad) ?>
		</div>

		<div class="hb-mbe-4 hb-lg-mbe-5" id="ustredi">
			<h2>
				Ústředí
			</h2>

			<p class="hb-block-text hb-mbe-3">
				Ústředí Hnutí Brontosaurus najdete v Brně.
				Kontakt na jednotlivé členy vedení <a href="/kontakty">najdete zde</a>.
			</p>

			<h3 class="hb-fw-b hb-lg-ta-c">
				VV, KRK a Stanovy
			</h3>

			<p class="hb-block-text">
				Činnost Hnutí Brontosaurus řídí Výkonný výbor volený Valnou hromadou Hnutí Brontosaurus a Rada Hnutí Brontosaurus. Předsedkyní Hnutí Brontosausaurus je Alena Konečná, místopředsedkyněmi jsou Rozálie Jandová a Daniela Syrovátková. Kontrolní činnost vykonává Kontrolní a revizní komise, její předsedou je Martin Polášek.
			</p>
		</div>

		<div class="hb-mbe-4 hb-lg-mbe-5" id="regionalni-centra">
			<h2>
				Regionální centra
			</h2>

			<p class="hb-block-text">
				Regionální centra Hnutí Brontosaurus se &ndash; jak už z názvu vyplývá &ndash; věnují převážně projektům a aktivitám
				v daném regionu. Podporují činnost základních článků působících v jejich blízkosti, nejsou jim však nijak
				nadřízena. V současnosti máme regionální centra v Praze, Jeseníkách a na Podluží.
			</p>
		</div>

		<div class="hb-mbe-4 hb-lg-mbe-5" id="zakladni-clanky">
			<h2>
				Základní články
			</h2>

			<p class="hb-block-text hb-mbe-3">
				Základní článek Hnutí Brontosaurus je uskupení lidí, kteří společně pořádají akce a chtějí
				vystupovat pod hlavičkou Hnutí Brontosaurus. Jednotlivé základní články jsou tak pestré jako lidé,
				kteří je tvoří. Spojuje je však otevřenost vůči novým členům, kteří se chtějí aktivně zapojit
				do jejich činnosti.
			</p>

			<p class="hb-block-text hb-mbe-3">
				Některé články pečují o přírodu a významné lokality, jiné pomáhají na ekofarmách, opravují
				hrady nebo zámky. Další se věnují environmentální výchově dětí v oddílech, sjíždí řeky, jezdí
				na vandry nebo třeba pořádají kulturní akce.
			</p>

			<p class="hb-block-text hb-mbe-3">
				Průměrně mají okolo 30 členů, jsou ale i výjimky, jejichž členská základna sahá přes stovku.
				Některé tvoří lidé doslova z celé republiky a schází se téměř výhradně na dobrovolnických
				akcích, jiné působí spíše lokálně a jejich členové pořádají také různé výlety, besedy
				nebo společenské večery.
			</p>

			<div class="about__presentationBox presentationBox presentationBox--textOnRight hb-mw-40 hb-lg-mi-auto hb-mb-4 [ hb-lg-d-f hb-lg-fd-c hb-lg-jc-c ]" style="--presentationBox-background-image-url: url('https://brontosaurus.cz/wp-content/uploads/2024/12/baseUnit.jpg')">
				<p class="presentationBox__text">
					<b>Na jakoukoliv brontosauří akci může přijet kdokoliv</b>, pokud se však budeš chtít zapojit více
					a třeba i nějakou akci také pomáhat pořádat, můžeš se přidat k některému ze základních článků (<a href="#mapa-zakladni-clanky">mapa</a>,
					<a href="/o-brontosaurovi/hnuti-brontosaurus/zakladni-clanky-a-kluby">seznam</a>).
				</p>
			</div>

			<p class="hb-block-text">
				A pokud máš přátele, s nimiž chceš společně podnikat akce a založit si svůj vlastní článek,
				napiš nám na <a href="mailto:hnuti@brontosaurus.cz" target="_blank">hnuti@brontosaurus.cz</a>.
			</p>
		</div>

		<div class="hb-mbe-4 hb-lg-mbe-5" id="kluby">
			<h2>
				Kluby
			</h2>

			<p class="hb-block-text">
				Kluby jsou snadnou cestou, jak začít s přáteli organizovat vlastní akce pod hlavičkou Hnutí
				Brontosaurus. Založení klubu je oproti základnímu článku jednodušší, protože odpadá většina
				administrativních povinností. Nebudete však samostatnou organizací, ale součástí ústředí
				Hnutí Brontosaurus. Pokud o tom uvažuješ, napiš nám na <a href="mailto:hnuti@brontosaurus.cz" target="_blank">hnuti@brontosaurus.cz</a>.
			</p>
		</div>

		<div class="hb-mbe-4 hb-lg-mbe-5" id="detske-oddily">
			<h2>
				BRĎO – Brontosauří dětské oddíly
			</h2>

			<p class="hb-block-text">
				Brontosaurus nezapomíná ani na ty nejmenší. Brontosauří dětské oddíly BRĎO jsou
				pravidelně se setkávající kluby, které pořádají i dětské tábory a další akce. Díky BRĎU mohou
				i nejmenší brontosauři pomoci přírodě a zároveň se naučit zajímat o svět kolem sebe. Oddíly
				Brďo vedou zkušení organizátoři proškolení naším interním kurzem organizátora BRĎO.
			</p>
		</div>

		<div class="hb-mbe-4 hb-lg-mbe-5" id="dobrovolnici">
			<h2>
				Dobrovolníci
			</h2>

			<p class="hb-block-text">
				Dobrovolníkem se stáváš, když se zúčastníš jakékoliv <a href="/dobrovolnicke-akce">brontosauří akce</a> a pomůžeš. Pokud chceš,
				můžeš se stát i oficiálním členem Hnutí Brontosaurus. Napiš nám na <a href="mailto:hnuti@brontosaurus.cz" target="_blank">hnuti@brontosaurus.cz</a>.
			</p>
		</div>

		<a class="hb-action hb-action--ulterior" href="https://www.mapotic.com/lokality-hnuti-brontosaurs" rel="noopener noreferrer" target="_blank">
			Mapa lokalit Hnutí Brontosaurus (portál Mapotic)
		</a>
	</article>
</main>
