<?php

use HnutiBrontosaurus\Theme\Assets;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\PostTypeInitializer;
use HnutiBrontosaurus\Theme\Rewrites\Event;
use HnutiBrontosaurus\Theme\Rewrites\Opportunity;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventCollectionDC;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventDC;
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
		wp_enqueue_style(
			handle: "hb-unpkg-glightbox",
			src: "https://unpkg.com/glightbox@3.2.0/dist/css/glightbox.min.css",
			ver: null,
		);

		Assets::staticScript('expandable', $theme);
		Assets::staticScript('lazyLoad', $theme);
		Assets::staticScript('menuHandler', $theme);
		Assets::staticScript('lightbox', $theme);
		Assets::script('references', $theme);
		Assets::script('administrativeUnitsMap', $theme);
		Assets::style('style', $theme);
	});

	add_action('enqueue_block_editor_assets', function () use ($theme) {
		Assets::script('editor', $theme);
		Assets::style('editor', $theme);
	});

})($hb_container, wp_get_theme());


function hb_administrative_units_map(string $administrationUnitsInJson, bool $hasBeenUnableToLoad)
{
?>
<div class="administrativeUnitsMap">
<?php if ($hasBeenUnableToLoad): ?>
	<div class="noResults">
		Promiňte, zrovna&nbsp;nám vypadl systém, kde&nbsp;máme uloženy všechny informace o&nbsp;našich článcích.
		Zkuste&nbsp;to prosím za&nbsp;chvilku znovu.
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
			data-organizationalUnits='<?php echo $administrationUnitsInJson ?>'
	></div>
<?php endif; ?>
</div>
<?php
}


function hb_references(?string $link = null)
{
?>
<div class="references" data-references>
	<button class="references__button references__button--previous button button--customization" data-references-button="previous" type="button" aria-hidden="true"></button>
	<button class="references__button references__button--next button button--customization" data-references-button="next" type="button" aria-hidden="true"></button>

	<?php if ($link !== null): ?><a class="references__link" href="<?php echo $link ?>"><?php endif; ?>
		<ul class="references__list" data-references-slides style="--carouselPosition: 1">
			<li class="references__list-item presentationBox" style="--presentationBox-background-image-url: url('https://brontosaurus.cz/wp-content/uploads/2024/12/aboutCrossroad-references-bohuslav-binka.jpg')">
				<div class="presentationBox__text">
					<div class="hb-fs-l hb-fw-b">
						doc.&nbsp;Mgr.&nbsp;Bohuslav Binka,&nbsp;Ph.D.
					</div>

					<div class="hb-fs-xs hb-mbns-2">
						vedoucí katedry environmentálních studií&nbsp;MU
					</div>
				</div>

				<div class="hb-fs-s presentationBox__text presentationBox__quotation quotation">Hnutí Brontosaurus pro&nbsp;mě vždy bylo spojené se&nbsp;slovy mládí, příroda, pomoc, zábava, letní tábor, smysl, fajn. A&nbsp;to je kombinace, která prostě láká. Ať&nbsp;láká i&nbsp;další generace tak, jako lákala tu&nbsp;moji.</div>
			</li>

			<li class="references__list-item presentationBox presentationBox--textOnRight" style="--presentationBox-background-image-url: url('https://brontosaurus.cz/wp-content/uploads/2024/12/aboutCrossroad-references-dalajlama.jpg')">
				<div class="presentationBox__text">
					<div class="hb-fs-l hb-fw-b">
						Dalajláma
					</div>

					<div class="hb-fs-xs hb-mbns-2">
						tibetský duchovní vůdce
					</div>
				</div>

				<div class="hb-fs-s presentationBox__text presentationBox__quotation quotation">Jsem opravdu šťastný, že jsme s&nbsp;vámi z&nbsp;České republiky navázali takto výjimečný vztah, čehož&nbsp;si velmi cením.</div>
			</li>

			<li class="references__list-item presentationBox" style="--presentationBox-background-image-url: url('https://brontosaurus.cz/wp-content/uploads/2024/12/aboutCrossroad-references-ladislav-tesar.jpg')">
				<div class="presentationBox__text">
					<div class="hb-fs-l hb-fw-b">
						Ladislav Tesař
					</div>

					<div class="hb-fs-xs hb-mbns-2">
						starosta Českého Sela, krajanské obce v&nbsp;Srbsku
					</div>
				</div>

				<div class="hb-fs-s presentationBox__text presentationBox__quotation quotation">S&nbsp;Brontosaury spolupracujeme velmi rádi. Ceníme&nbsp;si jejich pomoci s&nbsp;péčí o&nbsp;obec i&nbsp;naše drobné památky. Když&nbsp;přijedou, vždy&nbsp;to tady velmi ožije a&nbsp;ve&nbsp;vsi je na&nbsp;čas veseleji. Doufám, že&nbsp;budou přijíždět i&nbsp;v&nbsp;dalších letech.</div>
			</li>

			<li class="references__list-item presentationBox presentationBox--textOnRight" style="--presentationBox-background-image-url: url('https://brontosaurus.cz/wp-content/uploads/2024/12/aboutCrossroad-references-jaroslav-pavlicek.jpg')">
				<div class="presentationBox__text">
					<div class="hb-fs-l hb-fw-b">
						Jaroslav Pavlíček
					</div>

					<div class="hb-fs-xs hb-mbns-2">
						český polárník, cestovatel a spisovatel
					</div>
				</div>

				<div class="hb-fs-s presentationBox__text presentationBox__quotation quotation">Brontosaurům fandím, protože jejich akce, stejně&nbsp;jako pobyt v&nbsp;drsné přírodě, člověka výborně připraví na&nbsp;skutečný, ne&nbsp;virtuální život. Navíc umí i&nbsp;z&nbsp;dobrovolnictví udělat zábavu a&nbsp;vždy je za&nbsp;nimi vidět kus&nbsp;práce.</div>
			</li>
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
			Ještě by&nbsp;tě mohlo zajímat
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
				Kurzy a&nbsp;přednášky
			</h2>

			<ul class="related-list-item-list">
				<li class="related-list-item-list-item">
					Kurzy a&nbsp;workshopy
				</li>

				<li class="related-list-item-list-item">
					Přednášky a&nbsp;diskuze
				</li>

				<li class="related-list-item-list-item">
					Výukové programy pro&nbsp;školy
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
				Zážitkové a&nbsp;dobrovolnické akce
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
		<a class="related-list-item related-list-item--meetups button--secondary-wrapper" href="/setkavani-a-kluby">
			<h2 class="related-list-item-heading">
				Setkávání a&nbsp;kluby
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
					E-shop s&nbsp;dárky přírodě
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
				Pro&nbsp;děti i&nbsp;rodiče
			</h2>

			<ul class="related-list-item-list">
				<li>
					Tábory pro&nbsp;děti
				</li>

				<li>
					Dětské oddíly
				</li>

				<li>
					Akce pro&nbsp;rodiče s&nbsp;dětmi
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
		$counter = 0;
		foreach ($eventCollection as $event) {
			hb_event($event, lazyLoading: $lazyLoading, smaller: $smaller);

			if ( ! $inFutureView) {
				if ($counter === $eventsDisplayedOnLoad) {
					$counter++;
					break;
				}
			}
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
				$counter = 0;
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
				Promiňte, zrovna&nbsp;nám vypadl systém, kde&nbsp;máme uloženy všechny informace o&nbsp;plánovaných akcích.
				Zkuste&nbsp;to prosím za&nbsp;chvilku znovu.
			<?php else: ?>
				Zrovna&nbsp;tu žádné akce nemáme, ale zkus&nbsp;to později…
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
			<?php foreach ($event->tags as $tag): ?>
			<span class="hb-event__tag eventTag">
				<?php echo $tag ?>
			</span>
			<?php endforeach; ?>
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
		echo nl2br(Strings::truncate(strip_tags($event->invitation->introduction), $charsCount));
		?>
	</p>
</a>
<?php
}


/** @param \HnutiBrontosaurus\Theme\DataContainers\Events\Label[] $labels */
function hb_eventLabels(array $labels)
{
?>
<div class="eventLabels">
<?php foreach ($labels as $label): ?>
	<div class="eventLabels__item<?php if ($label->hasSelectorModifier): ?> eventLabels__item--type eventLabels__item--<?php echo $label->hasSelectorModifier; endif; ?>">
		<?php echo $label->label ?>
	</div>
<?php endforeach; ?>
</div>
<?php
}