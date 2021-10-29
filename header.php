<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;


//$hb_enableTracking = set_query_var('enableTracking', );
$hb_currentPost = get_post();
$hb_isOnSearchResultsPage = $hb_currentPost?->post_name === 'vysledky-vyhledavani';
$hb_pageClassSelector = $hb_currentPost->post_name;

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

	<script type="text/javascript">
		// Google Custom Search
		(function() {
			var cx = '002857815640148702223:d1mce-dceb4';
			var gcse = document.createElement('script');
			gcse.type = 'text/javascript';
			gcse.async = true;
			gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(gcse, s);
		})();
	</script>
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
					<gcse:searchbox-only
						resultsUrl="{$searchResultsPageLink}"
						queryParameterName="q"
					/>

					<script type="text/javascript">
						window.addEventListener('load', function () {
							document.getElementById('gsc-i-id1').placeholder = 'Prohledat tento web';
							document.getElementById('___gcse_0').querySelector('button.gsc-search-button').innerHTML = 'Hledat';
						});
					</script>
				</div>

				<button class="searchForm__closeButton" id="searchBarCloseButton" title="Zavřít vyhledávání" type="button"></button>
			</div>
		</div>
		<?php endif; ?>

		<header class="header" id="header" role="banner">
			<a class="header-logo" href="<?php echo get_home_url(); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 656.49 195.25" aria-label="Hnutí Brontosaurus">
					<g id="brontik">
						<path d="M191.68,222.28h0M60.6,209.41c1.6,1.77,12.91,12,27.53-3.54,2.07-1.85,4.35-6.52,4.35-6.52s16.86-36.53,17.43-37.91c7.79-18.05,29.26-50.72,56.74-.32,3.47,4.2,2.61,2,1.75-.31-20-39.9-43.93-39.43-61.89,2.39,0,.52-15.65,34-16.12,34.88a25.21,25.21,0,0,1-5,7.08s-13.48,13.48-24.19.43c-5.26-7.17-4.84-1.88-4.53.93,8.6,31.2,31.17,24.76,31.17,24.76-.11.8-1.06,3.35-1.1,3.94s-.1,1.21.49,1.67,41.14,1.69,42.18,1.72c7.1-.16,9.28-4.21,9.86-8.39.16-.15.39-.06.57,0a5.77,5.77,0,0,0,3.82-.27c.11,0-1.06,1.62-2.19,3.52-1.12,3.12,1,4.47,3.13,5.36,10.74.3,43.08.57,43.34.53,7.05-.49,7.47-2.36,9.4-3.9.22-.06.14.51,1.59.39s18.62-.15,18.89-.23a9.51,9.51,0,0,0,5.32-3.61c1.4-2.16,2.23-8.8-3.83-13.18-8.35-5.26-20.58-2.25-21.42,1.89-.62,2.08.11,3,1.57.84.24-.15.51-.32.82-.54,3.33-2.3,9.44-3.82,17.3,0,4.27,2.2,5.61,11.43-.39,11.84-5,.26-19.26.26-19.4.32-.47-1.76-1.66-20.22-26.17-12.63,0-.18-.06-1-.64-.75s-4.19,4.15-1.83,4.65c0,.33,3-1.11,3.14-1.35,3.83-3.09,15.75-2.57,17.87.07,3.69,2.75,9.39,12.5-1.1,13.66-.6.1-40.26-.27-41.18-.36a7.78,7.78,0,0,1-2.54-.6c-1-.51-1.07-2.2-.43-3,.2-.25,8.81-13.05,9-13.48.76-1.52-1.11-3.39-3-.9-.75.84-5.25,8-5.58,8.28-.22,1.47-5.93,2.92-6.69-.2-.07-2.79-8.88-13.47-24.2-7,1.43-3.24-1.48-4.89-2.56-.47-.37,5.43.8,4.22.8,4.22s.49.48,1.84-1c2.69-2,10.88-3.8,17.52.18,3.42,1.52,8,12.2-2.8,13.66-.39,0-39-1.39-39.42-1.39-.27,0,2.94-8.43,3.44-9.4,1.21-4.48-2.62-3.28-3.32-.63-.23.55-1.15,3.59-1.66,3.84C68.22,232.54,60.5,209.76,60.6,209.41ZM185.83,70.74s-5.94,4.39-5.42,13.41c-.31,17.53-.31,13.9.34,37.79.39,15.82,2.25,40.71,4.31,49.22,1.26,6.63,7.38,26,11.24,36.34.4,1.58,1.84,6.47,1.38,7.3-.47,2,2,2.8,2.29-.08a25,25,0,0,0-1.58-7.88c-2.53-7.49-7.3-21.07-10.94-35.28-2.27-9.54-3.62-29.55-4.31-49.7-.35-13.44-1.07-23.81.14-39.57a14.08,14.08,0,0,1,5.89-11A43.05,43.05,0,0,0,195.86,70a18.74,18.74,0,0,0,9.3-6.46c5.94-9.85-3.62-18.71-18.13-15.62a6.48,6.48,0,0,0-3.31,1.78c-.19.53-.46,1.17.22,1.47a.73.73,0,0,0,.3.07A1.93,1.93,0,0,0,185,51c17.44-5.32,23.2,6.11,17.37,12.1-1.54,1.85-5.56,4.33-9.1,5.21-8.48,1.25-14.16-.23-22.73-8,.48.69-2.39-2.48-1.86.39a23.4,23.4,0,0,0,17.2,10.11M163.74,50.62a10.6,10.6,0,0,1,1.07-1.08s2.64-1.52,1.29.65-.54,1.13-1.13,2c-4.31,7.4,1.85,14.72,2.05,15.07,2.17,3.58,3.88,8,5.72,14.25,2.28,6,1.35,70.21-.93,84.23-.3,3-3.25,14.05-5.57,14.59,0,0-1.8-.23-.64-2,.48-.64,2.42-4.37,3.64-10.66,1.89-9.42,3.76-68.84,1.17-86.33-1.07-4-2.23-7-5.5-13.62-5.91-7.41-3.84-13.42-1.17-17.09m5.43-1.82c.08-.61.07-1.76.68-2.14a3.11,3.11,0,0,1,1-.15c.36.05.53.45.73.7a5,5,0,0,1,1.16,2.39,1,1,0,0,1-.54,1.11c-.44.14-.58-.44-.77-.7a1.75,1.75,0,0,0-1.06-.65l-.64,1.43C168.84,50.38,169.06,49.6,169.17,48.8Zm7.09-.21c.12-.88-.08-2.39,1.12-2.4a3.13,3.13,0,0,1,1.18.34,4.54,4.54,0,0,1,1,1.56c.13.25.34.46.44.71a1.35,1.35,0,0,1-.49,1.58c-.55.31-.65,0-.88-.46s-.68-1-1.18-1-.36,1.17-.94,1S176.22,48.91,176.26,48.59Zm-8.35-2.2c-.08.35-.46,2.87-.45,3.27a4,4,0,0,0,.36,1.67,8.65,8.65,0,0,0,1.78,1.86,3.71,3.71,0,0,0,3.5-.17,6.81,6.81,0,0,0,1.25-1.21,3,3,0,0,1,.81-.88,3.33,3.33,0,0,0,.89,1.23,9.32,9.32,0,0,0,2.29.82,7.5,7.5,0,0,0,2.47-.62,8.66,8.66,0,0,0,1.87-2.46,6.47,6.47,0,0,0,.1-1.2,5.22,5.22,0,0,0-1.17-2.23,10.18,10.18,0,0,0-1-1.48,2.55,2.55,0,0,0-.7-.4,7.94,7.94,0,0,0-3.57-.42c-.67.34-1,1.27-1.21,1.88-.12.25-.32.7-.64.74-.58.08-.93-1-1.23-1.33a3.34,3.34,0,0,0-2.75-1.2,2.68,2.68,0,0,0-1.87,1,2.39,2.39,0,0,0-.76,1.1" transform="translate(-56.55 -44.11)" fill="#00943e"/>
					</g>
					<g id="text">
						<g>
							<path d="M283.53,181.41c0,8.06-4.59,16.84-20.11,16.84H246.79a3.16,3.16,0,0,1-3.16-3.37V141.71a3.16,3.16,0,0,1,3.16-3.36h14.59c8.47,0,19.19,2.45,19.19,16.53a14.82,14.82,0,0,1-5.72,12.24V168C280.67,170.29,283.53,175.29,283.53,181.41Zm-28.37-18.16h4.28c3.88,0,9.6-.41,9.6-8.07,0-6-5.51-6.83-9.6-6.83h-4.28Zm16.84,17c0-6.12-5.41-7-9.6-7h-7.24v15h7.24C266.28,188.25,272,187.74,272,180.29Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M316.69,165.8a3,3,0,0,1-2.75,1.83,5.19,5.19,0,0,1-2.35-.71,8.14,8.14,0,0,0-3.47-.71c-3.57,0-6.43,3.77-6.43,10.1V195a3.17,3.17,0,0,1-3.26,3.27h-5a3.11,3.11,0,0,1-3.27-3.27V160.39a3.22,3.22,0,0,1,3.27-3.47H295a3.12,3.12,0,0,1,2.75,1.43l3.17,4.18h.81c.21-.92,1.63-6.22,10-6.22a9.4,9.4,0,0,1,5.31,1.32,3.37,3.37,0,0,1,1.53,2.76,7,7,0,0,1-.51,1.94Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M322.1,181.31V174c0-8.16,4.18-17.65,16.94-17.65,13,0,17,9.49,17,17.65v7.35c0,8.06-4.08,17.55-17,17.55C326.28,198.86,322.1,189.37,322.1,181.31Zm22.45,1.53V172.53c0-3.67-1.63-6.22-5.51-6.22-3.67,0-5.41,2.55-5.41,6.22v10.31c0,3.67,1.74,6.23,5.41,6.23C342.92,189.07,344.55,186.51,344.55,182.84Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M387,172.94c0-3.67-1.73-6.63-5.51-6.63-3.57,0-5.71,3-5.71,6.63v22a3.11,3.11,0,0,1-3.27,3.27h-5a3.1,3.1,0,0,1-3.26-3.27V160.39a3.21,3.21,0,0,1,3.26-3.47h1.43a3.36,3.36,0,0,1,3,1.43l3.06,4h.82c.2-.92,3.47-6,9.69-6,9.08,0,13.06,7.65,13.06,15.71v23a3.19,3.19,0,0,1-3.36,3.27h-5A3.15,3.15,0,0,1,387,195Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M412.21,156.92v-14.7a3.1,3.1,0,0,1,3.26-3.26h5a3.17,3.17,0,0,1,3.27,3.26v14.7h7.55a3.11,3.11,0,0,1,3.27,3.27V163a3.11,3.11,0,0,1-3.27,3.27h-7.55V180.8c.41,7.35,1.94,8.16,5.2,8.16,1.33,0,.51-.1,2-.1a3.06,3.06,0,0,1,2.76,1.84l.92,2.55a7,7,0,0,1,.51,1.94,3.34,3.34,0,0,1-1.53,2.75c-1.74.92-4.49.92-7,.92-8.06,0-14.39-3.88-14.39-18.06V166.31H406.9a3.16,3.16,0,0,1-3.26-3.27v-2.85a3.16,3.16,0,0,1,3.26-3.27Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M440.78,181.31V174c0-8.16,4.18-17.65,16.94-17.65,13,0,17,9.49,17,17.65v7.35c0,8.06-4.08,17.55-17,17.55C445,198.86,440.78,189.37,440.78,181.31Zm22.45,1.53V172.53c0-3.67-1.63-6.22-5.51-6.22-3.67,0-5.41,2.55-5.41,6.22v10.31c0,3.67,1.74,6.23,5.41,6.23C461.6,189.07,463.23,186.51,463.23,182.84Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M483.64,195.7a3.8,3.8,0,0,1-1.53-2.76,20.87,20.87,0,0,1,.82-5.1,2.73,2.73,0,0,1,2.24-1.94c1.33,0,5.72,3.27,10.72,3.27,3.77,0,5.91-.31,5.91-2.76,0-6.12-20.41-4.59-20.41-18.16,0-6.94,3.37-11.94,16.44-11.94,2.55,0,8.67,0,11.42,2.55a3.35,3.35,0,0,1,1.54,2.75,17.19,17.19,0,0,1-.82,4.19,2.75,2.75,0,0,1-2.25,1.94c-2.14,0-6.22-1.43-9.89-1.43s-4.9,0-4.9,1.94c0,4.49,20.41,4.79,20.41,17.35,0,7.85-4.39,13.26-17.45,13.26C492,198.86,486.6,198.05,483.64,195.7Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M552.62,195.39a3.33,3.33,0,0,1-3.26,3.47h-1.63a3.8,3.8,0,0,1-2.76-1.53L541.91,194h-.82c-.1.82-1.63,4.9-8.57,4.9-8.16,0-14.08-4.18-14.08-13.37,0-1.53.1-12.55,13.16-12.55,6.74,0,8.57,3.57,8.68,4.49h.81v-3.06c-.41-7.35-4.38-8.67-7.65-8.67-1.33,0-6.33.61-7.86.61a2.71,2.71,0,0,1-2.75-1.84l-.41-2.24a15.65,15.65,0,0,1-.21-2.15,3.36,3.36,0,0,1,1.53-2.75c1.74-.92,9.49-1,12-1,8.06,0,16.83,3.88,16.83,18.06Zm-12.14-9.49c0-2.24-2.65-3.57-5.2-3.57S530,183,530,186.11c0,1.32.61,3.57,5.31,3.57C537.93,189.58,540.48,188.35,540.48,185.9Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M572.93,183c0,3.68,1.84,6.64,5.61,6.64,3.57,0,5.72-3,5.72-6.64V160.19a3.1,3.1,0,0,1,3.26-3.27h5a3.11,3.11,0,0,1,3.27,3.27v34.59a3.22,3.22,0,0,1-3.27,3.47h-1.43a3.38,3.38,0,0,1-3-1.43l-3.06-4h-.81c-.21.92-3.47,6-9.7,6-9.08,0-13.27-7.65-13.27-15.71v-23a3.19,3.19,0,0,1,3.37-3.27h5.11a3.14,3.14,0,0,1,3.16,3.27Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M632,165.8a3.06,3.06,0,0,1-2.76,1.83,5.19,5.19,0,0,1-2.35-.71,8.11,8.11,0,0,0-3.47-.71c-3.57,0-6.43,3.77-6.43,10.1V195a3.16,3.16,0,0,1-3.26,3.27h-5a3.11,3.11,0,0,1-3.27-3.27V160.39a3.22,3.22,0,0,1,3.27-3.47h1.53a3.12,3.12,0,0,1,2.75,1.43l3.17,4.18H617c.21-.92,1.64-6.22,10-6.22a9.35,9.35,0,0,1,5.3,1.32,3.37,3.37,0,0,1,1.53,2.76,6.64,6.64,0,0,1-.51,1.94Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M650.08,183c0,3.68,1.83,6.64,5.61,6.64,3.57,0,5.71-3,5.71-6.64V160.19a3.11,3.11,0,0,1,3.27-3.27h5a3.11,3.11,0,0,1,3.27,3.27v34.59a3.22,3.22,0,0,1-3.27,3.47h-1.43a3.37,3.37,0,0,1-3-1.43l-3.06-4h-.82c-.2.92-3.47,6-9.69,6-9.08,0-13.27-7.65-13.27-15.71v-23a3.19,3.19,0,0,1,3.37-3.27h5.1a3.15,3.15,0,0,1,3.17,3.27Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M683.34,195.7a3.83,3.83,0,0,1-1.53-2.76,21.42,21.42,0,0,1,.82-5.1,2.74,2.74,0,0,1,2.25-1.94c1.32,0,5.71,3.27,10.71,3.27,3.78,0,5.92-.31,5.92-2.76,0-6.12-20.41-4.59-20.41-18.16,0-6.94,3.37-11.94,16.43-11.94,2.55,0,8.67,0,11.43,2.55a3.34,3.34,0,0,1,1.53,2.75,16.65,16.65,0,0,1-.82,4.19,2.73,2.73,0,0,1-2.24,1.94c-2.14,0-6.23-1.43-9.9-1.43s-4.9,0-4.9,1.94c0,4.49,20.41,4.79,20.41,17.35,0,7.85-4.39,13.26-17.45,13.26C691.71,198.86,686.3,198.05,683.34,195.7Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
						</g>
						<g>
							<path d="M308.31,128.57h-9.13v11.56a1.72,1.72,0,0,1-1.81,1.81h-2.83a1.71,1.71,0,0,1-1.76-1.81V110.48a1.71,1.71,0,0,1,1.76-1.81h2.83a1.72,1.72,0,0,1,1.81,1.81v11.68h9.13V110.48a1.71,1.71,0,0,1,1.76-1.81h2.83a1.73,1.73,0,0,1,1.82,1.81v29.65a1.72,1.72,0,0,1-1.82,1.81h-2.83a1.71,1.71,0,0,1-1.76-1.81Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M332.75,127.89c0-2-1-3.69-3.07-3.69s-3.17,1.65-3.17,3.69v12.24a1.72,1.72,0,0,1-1.81,1.81h-2.78a1.72,1.72,0,0,1-1.82-1.81V120.91a1.79,1.79,0,0,1,1.82-1.92h.79a1.88,1.88,0,0,1,1.65.79l1.7,2.21h.45a6,6,0,0,1,5.39-3.34c5,0,7.25,4.25,7.25,8.73v12.75a1.76,1.76,0,0,1-1.87,1.81H334.5a1.74,1.74,0,0,1-1.75-1.81Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M350.43,133.5c0,2,1,3.68,3.12,3.68s3.18-1.64,3.18-3.68V120.8a1.72,1.72,0,0,1,1.81-1.81h2.78a1.72,1.72,0,0,1,1.81,1.81V140a1.78,1.78,0,0,1-1.81,1.92h-.8a1.87,1.87,0,0,1-1.64-.79l-1.7-2.21h-.45a6,6,0,0,1-5.39,3.35c-5,0-7.37-4.26-7.37-8.73V120.8a1.77,1.77,0,0,1,1.87-1.81h2.84a1.74,1.74,0,0,1,1.75,1.81Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M371,119v-8.17a1.73,1.73,0,0,1,1.82-1.81h2.78a1.75,1.75,0,0,1,1.81,1.81V119h4.19a1.72,1.72,0,0,1,1.82,1.81v1.59a1.73,1.73,0,0,1-1.82,1.81h-4.19v8c.23,4.08,1.08,4.54,2.89,4.54.74,0,.28-.06,1.13-.06a1.71,1.71,0,0,1,1.54,1l.5,1.42a3.56,3.56,0,0,1,.29,1.07,1.85,1.85,0,0,1-.85,1.53,9.11,9.11,0,0,1-3.91.52c-4.48,0-8-2.16-8-10v-8h-2.94a1.76,1.76,0,0,1-1.82-1.81V120.8a1.76,1.76,0,0,1,1.82-1.81Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
							<path d="M394.14,140.07a1.75,1.75,0,0,1-1.75,1.87H389.5a1.76,1.76,0,0,1-1.76-1.87V120.86A1.76,1.76,0,0,1,389.5,119h2.89a1.75,1.75,0,0,1,1.75,1.87Zm.06-27.72c-.51,1-1.25,2.33-3,2.33H390a1.76,1.76,0,0,1-1.76-1.87l2.1-5.39a2.88,2.88,0,0,1,2.32-1.87h3c.85,0,1.3.85,1.3,1.65a1.5,1.5,0,0,1-.11.68Z" transform="translate(-56.55 -44.11)" fill="#00943e"/>
						</g>
					</g>
				</svg>
			</a>

			<a class="header-toggleNavigationLink" href="#navigace" id="header-toggleNavigationLink">
				<svg class="header-toggleNavigationLink-image" viewBox="0 0 78 50" xmlns="http://www.w3.org/2000/svg" aria-label="Přejít na navigaci">
					<path fill="#000000" d="M0,0 h78 v8 h-78Z M0,21 h78 v8 h-78Z M0,42 h78 v8 h-78Z"></path>
				</svg>
			</a>

			<nav class="header-mainNavigation" role="navigation">
				<?php if($hb_currentPost?->post_name === 'english'): ?>
					<a class="header-mainNavigation-languageLink header-mainNavigation-languageLink--active" href="<?php echo get_home_url(); ?>" title="Česky" aria-label="Česky" data-label="Česky">
						<span class="header-mainNavigation-languageLink-label">
							Česky
						</span>
					</a>
				<?php else: ?>
					<a class="header-mainNavigation-languageLink" href="<?php echo getLinkFor('english'); ?>" title="English" aria-label="English" data-label="English">
						<span class="header-mainNavigation-languageLink-label">
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

			<a class="header-futureEventsLink<?php echo $hb_currentPost?->post_name === 'co-se-chysta' ? ' header-futureEventsLink--active' : ''; ?>" href="<?php echo getLinkFor('co-se-chysta'); ?>" title="Co se chystá">
				<span class="header-futureEventsLink-text">
					Co se chystá
				</span>
			</a>

			<?php if ( ! $hb_isOnSearchResultsPage): ?>
			<a class="header-searchBarToggler" href="#navigace" id="searchBarToggler" title="Vyhledávání"></a>
			<?php endif; ?>
		</header>
	</div>

	<div class="coverPhoto<?php echo $hb_pageClassSelector !== '' ? ' coverPhoto--' . $hb_pageClassSelector : ''; ?>"></div>

	<div class="content">