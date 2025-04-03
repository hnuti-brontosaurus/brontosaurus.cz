<?php

namespace HnutiBrontosaurus\Theme;


//$hb_enableTracking = set_query_var('enableTracking', );
$hb_currentPost = get_post();
$hb_isOnSearchResultsPage = $hb_currentPost?->post_name === 'vysledky-vyhledavani';
$hb_pageClassSelector = $hb_currentPost !== null ? $hb_currentPost->post_name : '';

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>

	<?php if($hb_enableTracking): ?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){ w[l]=w[l]||[];w[l].push({'gtm.start':
				new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-N39QMSV');</script>
	<!-- End Google Tag Manager -->
	<?php endif; ?>

	<script>
		(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
			key: "AIzaSyDsxejbWcsI1eb4eoQJq47Eq9qxvSCMXSc",
			v: "weekly",
		});
	</script>

	<!-- Google Custom Search -->
	<script async src="https://cse.google.com/cse.js?cx=4766a09e4b0ac4a34"></script>
</head>
<body <?php body_class(); ?>>
<?php if($hb_enableTracking): ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N39QMSV"
				  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php endif; ?>

<div class="header-paddingFixContainer">
	<div class="header-wrapper" id="header-wrapper">
		<?php if( ! $hb_isOnSearchResultsPage): ?>
		<div class="searchForm searchForm--hidden" id="searchBar">
			<div class="searchForm__innerWrapper">
				<div class="searchForm__gcseRoot">
					<div
						class="gcse-searchbox-only"
						data-resultsUrl="/vysledky-vyhledavani"
						data-queryParameterName="q"
						data-enableAutoComplete="false"
					></div>

					<script type="text/javascript">
						window.addEventListener('load', function () {
							document.getElementById('gsc-i-id1').placeholder = 'Prohledat tento web';
							const searchButtonEl = document.getElementById('___gcse_0').querySelector('button.gsc-search-button');
							searchButtonEl.classList.add('hb-action', 'hb-action--primary', 'hb-action--lg');
							searchButtonEl.innerHTML = 'Hledat';
						});
					</script>
				</div>

				<button class="searchForm__closeButton" id="searchBarCloseButton" title="Zavřít vyhledávání" type="button"></button>
			</div>
		</div>
		<?php endif; ?>

		<header class="header" id="header" role="banner">
			<a class="header__logo" href="<?php echo get_home_url(); ?>">
				<img src="<?php echo get_template_directory_uri(); ?>/images/logo-hb-full.png" alt="Hnutí Brontosaurus">
			</a>

			<a class="header__toggleNavigationLink" href="#navigace" id="header-toggleNavigationLink">
				<svg viewBox="0 0 78 50" xmlns="http://www.w3.org/2000/svg" aria-label="Přejít na navigaci">
					<path fill="#000000" d="M0,0 h78 v8 h-78Z M0,21 h78 v8 h-78Z M0,42 h78 v8 h-78Z"></path>
				</svg>
			</a>

			<nav class="header-mainNavigation" role="navigation">
				<?php if($hb_currentPost?->post_name === 'english'): ?>
					<a class="header-mainNavigation__languageLink header-mainNavigation__languageLink--active" href="<?php echo get_home_url(); ?>" title="Česky" aria-label="Česky" data-label="Česky">
						<span class="header-mainNavigation__languageLinkLabel">
							Česky
						</span>
					</a>
				<?php else: ?>
					<a class="header-mainNavigation__languageLink" href="/english" title="English" aria-label="English" data-label="English">
						<span class="header-mainNavigation__languageLinkLabel">
							English
						</span>
					</a>
				<?php endif; ?>

				<?php wp_nav_menu([
					'theme_location' => 'header',
					'container' => false,
					'depth' => 1,
				]); ?>
				<?php // TODO add rel="noopener noreferrer" target="_blank" to external links ?>
			</nav>

			<a class="header__futureEventsLink<?php echo $hb_currentPost?->post_name === 'co-se-chysta' ? ' header__futureEventsLink--active' : ''; ?>" href="/co-se-chysta" title="Co se chystá">
				<span class="header__futureEventsLinkText">
					Co se chystá
				</span>
			</a>

			<?php if ( ! $hb_isOnSearchResultsPage): ?>
			<a class="header__searchBarToggler" href="#navigace" id="searchBarToggler" title="Vyhledávání"></a>
			<?php endif; ?>
		</header>
	</div>

	<?php if (!in_array($hb_pageClassSelector, ['zapoj-se', 'podpor-nas', 'co-se-chysta'])): ?>
	<div class="coverPhoto<?php echo $hb_pageClassSelector !== '' ? ' coverPhoto--' . $hb_pageClassSelector : ''; ?>"></div>
	<?php endif; ?>

	<div class="content">
