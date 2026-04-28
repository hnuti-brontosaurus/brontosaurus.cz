<?php

use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\EventNotFound;
use HnutiBrontosaurus\BisClient\OpportunityNotFound;
use HnutiBrontosaurus\Theme\Assets;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\Meta;
use HnutiBrontosaurus\Theme\PostTypeInitializer;
use HnutiBrontosaurus\Theme\Rewrites\Event;
use HnutiBrontosaurus\Theme\Rewrites\Opportunity;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventDC;
use HnutiBrontosaurus\Theme\DataContainers\OpportunityDC;
use Nette\Utils\Strings;

/** @var Container $hb_container */
$hb_container = require_once __DIR__ . '/bootstrap.php';

require_once __DIR__ . '/hb-events/hb-events.php';
require_once __DIR__ . '/homepage-banner.php';


(function (Container $container, WP_Theme $theme) {

	/**
	 * turn off <link rel="canonical"> produced by rank math seo plugin
	 * that's because on `/akce/ID` and `/prilezitost/ID` it generates bad URL (with no ID) and I couldn't find any simple way to modify it inside of controller
	 */
	add_filter('rank_math/frontend/canonical', static fn($canonical) => null);


	add_action('init', function () use ($container) {
		Event::rewriteRule();
		Opportunity::rewriteRule();

		PostTypeInitializer::novinky();
		PostTypeInitializer::pribehyNadseni();
		PostTypeInitializer::kontakty();

		Meta::coverPhoto();

		register_nav_menus([
			'header' => __('Hlavička'),
			'footer-left' => __('Patička – vlevo'),
			'footer-center' => __('Patička – uprostřed'),
			'footer-right' => __('Patička – vpravo'),
		]);

		hb_events($container);
	});

	add_filter('query_vars', function($vars) {
		Event::queryVars($vars);
		Opportunity::queryVars($vars);
		return $vars;
	});

	add_action('after_switch_theme', function () {
		Event::rewriteRule();
		Opportunity::rewriteRule();
		flush_rewrite_rules();
	});

	add_action('wp_head', function () use ($container) {
		if (is_singular()) {
			$bisClient = $container->getBisClient();
			$dateFormatForHuman = $container->getDateFormatForHuman();
			$dateFormatForRobot = $container->getDateFormatForRobot();

			global $post;
			if ($post->post_name === 'akce') {
				$eventId = (int) get_query_var('eventId');
				try {
					$event = $bisClient->getEvent($eventId);
					$eventDC = new EventDC($event, $dateFormatForHuman, $dateFormatForRobot);
					hb_akce_meta($eventDC);
				}
				catch (EventNotFound) {}
				catch (ConnectionToBisFailed) {}
			}
			elseif ($post->post_name === 'prilezitost') {
				$opportunityId = (int) get_query_var('opportunityId');
				try {
					$opportunity = $bisClient->getOpportunity($opportunityId);
					$opportunityDC = new OpportunityDC($opportunity);
					hb_prilezitost_meta($opportunityDC);
				}
				catch (OpportunityNotFound) {}
				catch (ConnectionToBisFailed) {}
			}
		}
	});

	add_action('after_setup_theme', function () {
		add_theme_support('post-thumbnails');
		add_theme_support('title-tag');
	});

	add_action('wp_enqueue_scripts', function () use ($theme) {
		wp_enqueue_script(
			handle: "hb-unpkg-headroom",
			src: "https://unpkg.com/headroom.js@0.12.0/dist/headroom.min.js",
			ver: null,
		);
		wp_enqueue_script(
			handle: "hb-unpkg-lazyload",
			src: "https://unpkg.com/vanilla-lazyload@17.9.0/dist/lazyload.min.js",
			ver: null,
		);
		wp_enqueue_script(
			handle: "hb-unpkg-glightbox",
			src: "https://unpkg.com/glightbox@3.2.0/dist/js/glightbox.min.js",
			ver: null,
		);

		Assets::sanitizecss();
		wp_enqueue_style(
			handle: "hb-unpkg-glightbox",
			src: "https://unpkg.com/glightbox@3.2.0/dist/css/glightbox.min.css",
			ver: null,
		);

		Assets::staticScript('expandable', $theme);
		Assets::staticScript('lazyLoad', $theme);
		Assets::staticScript('menuHandler', $theme);
		Assets::staticScript('lightbox', $theme);
		Assets::staticScript('administrativeUnitsMap', $theme);
		Assets::staticScript('references', $theme);

		Assets::staticStyle('fonts', $theme);
		Assets::staticStyle('defaults', $theme);
		Assets::staticStyle('utils', $theme);
		Assets::style('style', $theme);
		Assets::staticStyle('components', $theme);
	});

	add_action('enqueue_block_editor_assets', function () use ($theme) {
		Assets::staticScript('editor', $theme);

		Assets::sanitizecss();
		Assets::style('editor', $theme);

		Assets::staticStyle('fonts', $theme);
		Assets::staticStyle('defaults', $theme);
		Assets::staticStyle('components', $theme);

		Assets::staticScript('hb-cover-photo-panel', $theme, [
			'wp-plugins',
			'wp-edit-post',
			'wp-element',
			'wp-compose',
			'wp-components',
			'wp-data',
			'wp-editor',
			'wp-block-editor',
			'wp-core-data',
		]);
	});

})($hb_container, wp_get_theme());


function hb_akce_meta(EventDC $event) { ?>
	<?php if ($event): ?>
	<meta property="og:locale" content="cs_CZ">
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?php echo $event->title ?>">
	<?php if ( HB_IS_ON_PRODUCTION): ?>
	<meta property="og:description" content="<?php echo hb_truncate(htmlspecialchars(strip_tags(str_replace("</p><p>", "</p> <p>", $event->invitation->introduction))), 150) ?>">
	<?php else: ?>
	<meta property="og:description" content="📅 <?php echo $event->dateSpan ?> 🌍 <?php echo $event->place->name ?>">
	<?php endif; ?>
	<meta property="og:url" content="<?php echo $event->link ?>">
	<meta property="og:site_name" content="Hnutí Brontosaurus">
	<?php if ($event->hasCoverPhoto): ?>
	<meta property="og:image" content="<?php echo $event->coverPhotoPath ?>">
	<?php endif; ?>
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo $event->title ?>">
	<?php if ( HB_IS_ON_PRODUCTION): ?>
	<meta name="twitter:description" content="<?php echo hb_truncate(htmlspecialchars(strip_tags(str_replace("</p><p>", "</p> <p>", $event->invitation->introduction))), 150) ?>">
	<?php else: ?>
	<meta name="twitter:description" content="📅 <?php echo $event->dateSpan ?> 🌍 <?php echo $event->place->name ?>">
	<?php endif; ?>
	<?php if ($event->hasCoverPhoto): ?>
	<meta name="twitter:image" content="<?php echo $event->coverPhotoPath ?>">
	<?php endif; ?>
	<?php endif; ?>
<?php }

function hb_prilezitost_meta(OpportunityDC $opportunity) { ?>
	<?php if ($opportunity): ?>
	<meta property="og:locale" content="cs_CZ">
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?php echo $opportunity->title ?>">
	<meta property="og:description" content="<?php echo hb_truncate(htmlspecialchars(strip_tags($opportunity->introduction)), 150) ?>">
	<meta property="og:url" content="<?php echo $opportunity->link ?>">
	<meta property="og:site_name" content="Hnutí Brontosaurus">
	<meta property="og:image" content="<?php echo $opportunity->coverPhotoPath ?>">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo $opportunity->title ?>">
	<meta name="twitter:description" content="<?php hb_truncate(htmlspecialchars(strip_tags($opportunity->introduction)), 150) ?>">
	<meta name="twitter:image" content="<?php echo $opportunity->coverPhotoPath ?>">
	<?php endif; ?>
<?php }

function hb_administrative_units_map(string $administrationUnitsInJson, bool $hasBeenUnableToLoad)
{
?>
<div class="administrativeUnitsMap">
<?php if ($hasBeenUnableToLoad): ?>
	<div class="noResults">
		Promiňte, zrovna nám vypadl systém, kde máme uloženy všechny informace o našich článcích.
		Zkuste to prosím za chvilku znovu.
	</div>
<?php else: ?>
	<ul class="administrativeUnitsMap__filters" id="administrativeUnitsMap-filters">
		<li class="administrativeUnitsMap__filter" id="mapa-vse">
			<a class="administrativeUnitsMap__filterLink button button--customization" href="#mapa-vse">
				vše
			</a>
		</li>

		<li class="administrativeUnitsMap__filter" id="mapa-ustredi" data-slug="office">
			<a class="administrativeUnitsMap__filterLink administrativeUnitsMap__filterLink--icon administrativeUnitsMap__filterLink--office button button--customization" href="#mapa-ustredi">
				ústředí
			</a>
		</li>

		<li class="administrativeUnitsMap__filter" id="mapa-regionalni-centra" data-slug="regional">
			<a class="administrativeUnitsMap__filterLink administrativeUnitsMap__filterLink--icon administrativeUnitsMap__filterLink--regional button button--customization" href="#mapa-regionalni-centra">
				regionální centra
			</a>
		</li>

		<li class="administrativeUnitsMap__filter" id="mapa-zakladni-clanky" data-slug="base">
			<a class="administrativeUnitsMap__filterLink administrativeUnitsMap__filterLink--icon administrativeUnitsMap__filterLink--base button button--customization" href="#mapa-zakladni-clanky">
				základní články
			</a>
		</li>

		<li class="administrativeUnitsMap__filter" id="mapa-kluby" data-slug="club">
			<a class="administrativeUnitsMap__filterLink administrativeUnitsMap__filterLink--icon administrativeUnitsMap__filterLink--club button button--customization" href="#mapa-kluby">
				kluby
			</a>
		</li>

		<li class="administrativeUnitsMap__filter" id="mapa-detske-oddily" data-slug="children">
			<a class="administrativeUnitsMap__filterLink administrativeUnitsMap__filterLink--icon administrativeUnitsMap__filterLink--children button button--customization" href="#mapa-detske-oddily">
				dětské oddíly BRĎO
			</a>
		</li>
	</ul>

	<div class="administrativeUnitsMap__map"
			id="map"
			data-administrativeUnits='<?php echo $administrationUnitsInJson ?>'
	></div>
<?php endif; ?>
</div>
<?php
}


function hb_references(?string $link = null)
{
	$references = [
		(object) [
			'name' => 'Pavel Čadek',
			'role' => 'písničkář s cellem',
			'quote' => 'Na brontosauří akce jsem jezdil nejdřív jako dítě a později jsem se vrátil jako dospělý. Organizace je mi velmi sympatická svou otevřeností, přijetím a starostí o přírodu. Přeju brontosaurům (hlavně mladým organizátorům) hodně trpělivosti, síly a nadšení pro vymýšlení stále nových věcí a táhnutí vozu dále!',
			'image' => 'https://brontosaurus.cz/wp-content/uploads/2025/03/pavel-cadek.jpg',
		],
		(object) [
			'name' => 'Jan Dusík',
			'role' => 'zástupce pro klimatickou politiku v Evropské komisi',
			'quote' => 'S Hnutím Brontosaurus jsem začal svou cestu k ochraně životního prostředí – spoluzakládal jsem dětský oddíl v Plzni, organizoval akce ke Dni Země a založil Ekologický právní servis. Účastnil jsem se akcí, vedl kancelář i koordinoval zahraniční vztahy. Na hnutí si vážím jeho nadšení a schopnosti inspirovat mladé lidi k ochraně přírody a budování přátelství.',
			'image' => 'https://brontosaurus.cz/wp-content/uploads/2025/03/jan-dusik.jpg',
		],
		(object) [
			'name' => 'Petra Kolínská',
			'role' => 'ředitelka Zeleného kruhu',
			'quote' => 'Pro mne představuje Hnutí Brontosaurus unikátní propojení ochrany přírody a budování otevřené a přátelské pospolitosti různorodých lidí. Brontosaurus je dokladem, že je mezi námi mnoho lidí, kteří mají odvahu a vůli měnit svět kolem sebe dobrovolnou prací. A velkou inspirací je pro mne jejich výdrž – rozvíjet něco padesát let není jen tak.',
			'image' => 'https://brontosaurus.cz/wp-content/uploads/2025/03/petra-kolinska.jpg',
		],
		(object) [
			'name' => 'Jitka Nováčková',
			'role' => 'influencerka',
			'quote' => 'Jakýkoliv krok směrem k obnově naší přírody a podpoře v její divokosti je krokem, který by měl být oceněn. Moc Vám, Brontosaři, děkuji.',
			'image' => 'https://brontosaurus.cz/wp-content/uploads/2025/03/jitka-novackova-2.jpg',
		],
		(object) [
			'name' => 'Jiří Čevela',
			'role' => 'frontman Circus Problem',
			'quote' => 'Hnutí Brontosaurus podporuji, protože ekologie je cool. Fandím vám, jste banda super cvoků, podobně jako my z CIRCUS PROBLEM. Od mládí se s vámi kámoším a jsem rád, že jsem mohl být součástí vašeho příběhu a péče o přírodu. Držím vám palce!',
			'image' => 'https://brontosaurus.cz/wp-content/uploads/2025/03/circus-problem.jpg',
		],
		(object) [
			'name' => 'Petr Pavel',
			'role' => 'prezident ČR',
			'quote' => 'Mnoho z nás si pod pojmem ochrana přírody představuje velké věci, úspěch přitom často tkví v maličkostech. Od sbírání odpadků, přes sázení stromů až po výchovu dětí k respektu k přírodě. Poděkování patří každému, komu svět kolem nás není lhostejný.',
			'image' => 'https://brontosaurus.cz/wp-content/uploads/2025/03/petr-pavel.jpg',
		],
		(object) [
			'name' => 'doc. Mgr. Bohuslav Binka, Ph.D.',
			'role' => 'vedoucí katedry environmentálních studií MU',
			'quote' => 'Hnutí Brontosaurus pro mě vždy bylo spojené se slovy mládí, příroda, pomoc, zábava, letní tábor, smysl, fajn. A to je kombinace, která prostě láká. Ať láká i další generace tak, jako lákala tu moji.',
			'image' => 'https://brontosaurus.cz/wp-content/uploads/2024/12/aboutCrossroad-references-bohuslav-binka.jpg',
		],
		(object) [
			'name' => 'Dalajláma',
			'role' => 'tibetský duchovní vůdce',
			'quote' => 'Jsem opravdu šťastný, že jsme s vámi z České republiky navázali takto výjimečný vztah, čehož si velmi cením.',
			'image' => 'https://brontosaurus.cz/wp-content/uploads/2024/12/aboutCrossroad-references-dalajlama.jpg',
		],
		(object) [
			'name' => 'Ladislav Tesař',
			'role' => 'starosta Českého Sela, krajanské obce v Srbsku',
			'quote' => 'S Brontosaury spolupracujeme velmi rádi. Ceníme si jejich pomoci s péčí o obec i naše drobné památky. Když přijedou, vždy to tady velmi ožije a ve vsi je na čas veseleji. Doufám, že budou přijíždět i v dalších letech.',
			'image' => 'https://brontosaurus.cz/wp-content/uploads/2024/12/aboutCrossroad-references-ladislav-tesar.jpg',
		],
		(object) [
			'name' => 'Jaroslav Pavlíček',
			'role' => 'český polárník, cestovatel a spisovatel',
			'quote' => 'Brontosaurům fandím, protože jejich akce, stejně jako pobyt v drsné přírodě, člověka výborně připraví na skutečný, ne virtuální život. Navíc umí i z dobrovolnictví udělat zábavu a vždy je za nimi vidět kus práce.',
			'image' => 'https://brontosaurus.cz/wp-content/uploads/2024/12/aboutCrossroad-references-jaroslav-pavlicek.jpg',
		],
	];
?>
<div class="hb-references" data-references data-references-infinite data-references-autoplay>
	<button class="hb-references__button hb-references__button--previous button button--customization" data-references-button="previous" type="button" aria-hidden="true"></button>
	<button class="hb-references__button hb-references__button--next button button--customization" data-references-button="next" type="button" aria-hidden="true"></button>

	<?php if ($link !== null): ?><a class="hb-references__link" href="<?php echo $link ?>"><?php endif; ?>
		<ul class="hb-references__list" data-references-slides>
			<?php
			$i = 0;
			foreach ($references as $reference):
			?>
			<li class="hb-references__list-item presentationBox<?php if ($i % 2 === 1): ?> presentationBox--textOnRight<?php endif; ?>" style="--presentationBox-background-image-url: url('<?php echo $reference->image; ?>')">
				<div class="presentationBox__text">
					<div class="hb-fs-l hb-fw-b">
						<?php echo $reference->name ?>
					</div>

					<div class="hb-fs-xs hb-mbns-2">
						<?php echo $reference->role ?>
					</div>
				</div>

				<div class="hb-fs-s presentationBox__text presentationBox__quotation"><?php echo $reference->quote ?></div>
			</li>
			<?php
			$i++;
			endforeach;
			?>
		</ul>
	</a>
</div>
<?php
}


function hb_related(
	?string $headingText = null,
	?string $subheadingText = null,
	?bool $excludeCourses = false,
	?bool $excludeVoluntary = false,
	?bool $excludeMeetups = false,
	?bool $excludeSupport = false,
	?bool $excludeForChildren = false,
)
{
?>
<section class="related">
	<h1 class="related-heading">
		<?php if ($headingText): ?>
			<?php echo $headingText ?>
		<?php else: ?>
			Ještě by tě mohlo zajímat
		<?php endif; ?>
	</h1>

	<?php if ($subheadingText !== null): ?>
	<div class="related-subheading">
		<?php echo $subheadingText ?>
	</div>
	<?php endif; ?>

	<div class="related-list">

		<?php if ( ! $excludeCourses): ?>
		<a class="related-list-item related-list-item--courses button--secondary-wrapper" href="/kurzy-a-prednasky">
			<h2 class="related-list-item-heading">
				Kurzy a přednášky
			</h2>

			<ul class="related-list-item-list">
				<li class="related-list-item-list-item">
					Kurzy a workshopy
				</li>

				<li class="related-list-item-list-item">
					Přednášky a diskuze
				</li>

				<li class="related-list-item-list-item">
					Výukové programy pro školy
				</li>
			</ul>

			<div class="related-list-item-roundedPhoto related-list-item--courses-roundedPhoto"></div>
			<div class="related-list-item-moreLink related-list-item--courses-moreLink button button--secondary">Chci více info
				<div class="related-list-item-moreLink-background related-list-item--courses-moreLink-background"></div>
			</div>
		</a>
		<?php endif; ?>

		<?php if ( ! $excludeVoluntary): ?>
		<a class="related-list-item related-list-item--voluntary button--secondary-wrapper" href="/dobrovolnicke-akce">
			<h2 class="related-list-item-heading">
				Zážitkové a dobrovolnické akce
			</h2>

			<ul class="related-list-item-list">
				<li>
					Jednodenní
				</li>

				<li>
					Víkendovky
				</li>

				<li>
					Prázdninové
				</li>
			</ul>

			<div class="related-list-item-roundedPhoto related-list-item--voluntary-roundedPhoto"></div>
			<div class="related-list-item-moreLink related-list-item--voluntary-moreLink button button--secondary">Chci více info
				<div class="related-list-item-moreLink-background related-list-item--voluntary-moreLink-background"></div>
			</div>
		</a>
		<?php endif; ?>

		<?php if ( ! $excludeMeetups): ?>
		<a class="related-list-item related-list-item--meetups button--secondary-wrapper" href="/setkavani">
			<h2 class="related-list-item-heading">
				Setkávání
			</h2>

			<ul class="related-list-item-list">
				<li>
					Klubová setkání
				</li>

				<li>
					Sportovní aktivity
				</li>

				<li>
					Herní večery
				</li>
			</ul>

			<div class="related-list-item-roundedPhoto related-list-item--meetups-roundedPhoto"></div>
			<div class="related-list-item-moreLink related-list-item--meetups-moreLink button button--secondary">Chci více info
				<div class="related-list-item-moreLink-background related-list-item--meetups-moreLink-background"></div>
			</div>
		</a>
		<?php endif; ?>

		<?php if ( ! $excludeSupport): ?>
		<a class="related-list-item related-list-item--donation button--secondary-wrapper" href="/podpor-nas">
			<h2 class="related-list-item-heading">
				Podpoř nás
			</h2>

			<ul class="related-list-item-list">
				<li>
					Podpora našich projektů
				</li>

				<li>
					Adopce Brontosaura
				</li>

				<li>
					E-shop s dárky přírodě
				</li>
			</ul>

			<div class="related-list-item-roundedPhoto related-list-item--donation-roundedPhoto"></div>
			<div class="related-list-item-moreLink related-list-item--donation-moreLink button button--secondary">Chci více info
				<div class="related-list-item-moreLink-background related-list-item--donation-moreLink-background"></div>
			</div>
		</a>
		<?php endif; ?>

		<?php if ( ! $excludeForChildren): ?>
		<a class="related-list-item related-list-item--children button--secondary-wrapper" href="/pro-deti">
			<h2 class="related-list-item-heading">
				Pro děti i rodiče
			</h2>

			<ul class="related-list-item-list">
				<li>
					Tábory pro děti
				</li>

				<li>
					Dětské oddíly
				</li>

				<li>
					Akce pro rodiče s dětmi
				</li>
			</ul>

			<div class="related-list-item-roundedPhoto related-list-item--children-roundedPhoto"></div>
			<div class="related-list-item-moreLink related-list-item--children-moreLink button button--secondary">Chci více info
				<div class="related-list-item-moreLink-background related-list-item--children-moreLink-background"></div>
			</div>
		</a>
		<?php endif; ?>
	</div>
</section>
<?php
}


function hb_eventList(EventCollectionDC $eventCollection, bool $lazyLoading = true, bool $inFutureView = false, bool $smaller = false)
{
?>
<div class="hb-eventList<?php if ($smaller): ?> hb-eventList--smaller<?php endif; ?>">
	<?php if ($eventCollection->hasAny): ?>
		<?php $eventsDisplayedOnLoad = 9; ?>

		<div class="hb-eventList__grid">
		<?php
		$counter = 1;
		foreach ($eventCollection as $event) {
			hb_event($event, lazyLoading: $lazyLoading, smaller: $smaller);

			if ( ! $inFutureView && $counter === $eventsDisplayedOnLoad) {
				break;
			}

			$counter++;
		}
		?>
		</div>

		<?php if ( ! $inFutureView && $eventCollection->count > $eventsDisplayedOnLoad): ?>
			<div class="hb-expandable">
				<button class="hb-eventList__moreLink button button--customization" type="button" data-hb-expandable-toggler data-hb-expandable-toggler-remove-on-expand>
					Zobrazit další
				</button>

				<div class="hb-eventList__grid" data-hb-expandable-content>
				<?php
				$counter = 1;
				foreach ($eventCollection as $event) {
					if ($counter <= $eventsDisplayedOnLoad) {
						$counter++;
						continue;
					}

					hb_event($event, smaller: $smaller);
				}
				?>
				</div>
			</div>
		<?php endif; ?>

	<?php else: ?>
		<div class="hb-eventList__noResults noResults">
			<?php if ($eventCollection->hasBeenUnableToLoad): ?>
				Promiňte, zrovna nám vypadl systém, kde máme uloženy všechny informace o plánovaných akcích.
				Zkuste to prosím za chvilku znovu.
			<?php else: ?>
				Zrovna tu žádné akce nemáme, ale zkus to později…
			<?php endif; ?>
		</div>

	<?php endif; ?>
</div>
<?php
}


function hb_event(EventDC $event, bool $lazyLoading = true, bool $smaller = false)
{
?>
<a class="hb-event<?php if ($event->isFull): ?> hb-event--full<?php endif; ?><?php if ($smaller): ?> hb-event--smaller<?php endif; ?>" href="<?php echo $event->link ?>">
	<div class="hb-event__imageWrapper">
		<img alt="" class="hb-event__image<?php if ( ! $event->hasCoverPhoto): ?> hb-event__image--noThumbnail<?php endif; ?>" <?php if ($lazyLoading): ?>data-<?php endif; ?>src="<?php if ($event->hasCoverPhoto): echo $event->coverPhotoPath; else: ?>https://brontosaurus.cz/wp-content/uploads/2024/12/logo-hb-brontosaurus.svg<?php endif; ?>">
		<noscript>
			<img alt="" class="hb-event__image<?php if ( ! $event->hasCoverPhoto): ?> hb-event__image--noThumbnail<?php endif; ?>" src="<?php if ($event->hasCoverPhoto): echo $event->coverPhotoPath; else: ?>https://brontosaurus.cz/wp-content/uploads/2024/12/logo-hb-brontosaurus.svg<?php endif; ?>">
		</noscript>

		<div class="hb-event__labels">
			<?php hb_eventLabels($event->labels) ?>
		</div>
	</div>

	<?php if ($event->isFull): ?>
	<div class="hb-event__full">
		plně obsazeno
	</div>
	<?php endif; ?>

	<header class="hb-event__header">
		<h4 class="hb-event__heading">
			<?php echo $event->title ?>
		</h4>

		<?php if ($event->tags): ?>
		<div class="hb-event__tags">
			<?php hb_tags($event->tags, 'hb-event__tag') ?>
		</div>
		<?php endif; ?>

		<div class="hb-event__meta">
			<time class="hb-event__date" datetime="<?php echo $event->dateStartForRobots ?>">
				<?php echo $event->dateSpan ?>
			</time>

			<span class="hb-event__place" title="Místo konání">
				<?php echo $event->place->name ?>
			</span>
		</div>
	</header>

	<p class="hb-event__excerpt">
		<?php
		$charsCount = $smaller ? 60 : 200;
		echo nl2br(Strings::truncate(hb_strip_tags($event->invitation->introduction), $charsCount));
		?>
	</p>
</a>
<?php
}


/** @param \HnutiBrontosaurus\Theme\DataContainers\Events\Label[] $labels */
function hb_eventLabels(array $labels)
{
?>
<div class="hb-eventLabels">
<?php foreach ($labels as $label): ?>
	<div class="hb-eventLabels__item<?php if ($label->hasSelectorModifier): ?> hb-eventLabels__item--type hb-eventLabels__item--<?php echo $label->selectorModifier; endif; ?>">
		<?php echo $label->label ?>
	</div>
<?php endforeach; ?>
</div>
<?php
}


/** @param string[] $tags */
function hb_tags(array $tags, ?string $className)
{
	foreach ($tags as $tag):
	?>
	<span class="<?php echo $className !== null ? $className . ' ' : ''; ?>hb-tag">
		<?php echo $tag ?>
	</span>
	<?php
	endforeach; 
}



// redirects listing
add_shortcode('rankmath_redirects', function () {
    global $wpdb;

    // Z tabulky redirections si vezmeme ID a stav + typ redirectu
    $table_main = $wpdb->prefix . 'rank_math_redirections';
    $table_cache = $wpdb->prefix . 'rank_math_redirections_cache';

    // h.status = 1 → aktivní
    // h.header typu 301/302 atd. nám nevadí
    // h.match_type = 'exact' → NENÍ regexp
    $redirects = $wpdb->get_results("
        SELECT c.src, c.url_to
        FROM $table_cache c
        JOIN $table_main h ON h.id = c.redirection_id
        WHERE h.status = 1
          AND h.match_type = 'exact'
        ORDER BY c.src ASC
    ");

    if (empty($redirects)) {
        return "<p>Žádné odpovídající přesměrování nebylo nalezeno.</p>";
    }

    $out = "<table>
                <tr>
                    <th>Zdrojová URL</th>
                    <th>Cílová URL</th>
                </tr>";

    foreach ($redirects as $r) {
        $src = esc_html($r->src);
        $to  = esc_url($r->url_to);

        $out .= "<tr>
                    <td>{$src}</td>
                    <td><a href=\"{$to}\" target=\"_blank\">{$to}</a></td>
                 </tr>";
    }

    $out .= "</table>";

    return $out;
});
