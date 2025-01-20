<?php

namespace HnutiBrontosaurus\Theme;

$hb_currentPost = get_post();

?>
</div>

<?php if ($hb_currentPost?->post_name !== 'nasi-partneri'): ?>
<aside class="sponsors hb-pi-4 hb-pb-5 hb-ta-c">
	<ul class="sponsors__list [ hb-d-g hb-rg-5 hb-cg-4 hb-ji-c hb-jc-c hb-ai-c ] [ hb-mi-auto hb-mbe-5 ] hb-lst-n">
		<li class="sponsors__item sponsors__item--msmt">
			<a href="http://www.msmt.cz" target="_blank">
				<img src="https://brontosaurus.cz/wp-content/uploads/2024/12/ministerstvo-skolstvi.png" alt="Ministerstvo školství, mládeže a tělovýchovy" class="sponsors__item-image">
			</a>
		</li>

		<li class="sponsors__item">
			<a href="http://www.erasmusprogramme.com" target="_blank">
				<img src="https://brontosaurus.cz/wp-content/uploads/2024/12/spolufinancovano-eu.png" alt="Spolufinancováno Evropskou unií" class="sponsors__item-image">
			</a>
		</li>

		<li class="sponsors__item sponsors__item--mv">
			<a href="https://www.mvcr.cz" target="_blank">
				<img src="https://brontosaurus.cz/wp-content/uploads/2024/12/ministerstvo-vnitra.png" alt="Ministerstvo vnitra České republiky" class="sponsors__item-image">
			</a>
		</li>

		<li class="sponsors__item sponsors__item--mzp">
			<a href="https://www.mzp.cz" target="_blank">
				<img src="https://brontosaurus.cz/wp-content/uploads/2024/12/ministerstvo-zivotniho-prostredi.png" alt="Ministerstvo životního prostředí" class="sponsors__item-image">
			</a>
		</li>

		<li class="sponsors__item">
			<a href="https://www.sfzp.cz" target="_blank">
				<img src="https://brontosaurus.cz/wp-content/uploads/2024/12/statni-fond.png" alt="Státní fond životního prostředí České republiky" class="sponsors__item-image">
			</a>
		</li>
	</ul>

	<span class="hb-fs-s">Děkujeme za podporu <a href="/nasi-partneri">všem našim partnerům</a>.</span>
</aside>
<?php endif; ?>

<div class="newsletter">
	<h2 class="newsletter__heading">
		Chceš zůstat v kontaktu?
	</h2>

	<div>
		<script>
			(function (w,d,s,o,f,js,fjs) {
				w['ecm-widget']=o;w[o] = w[o] || function () { (w[o].q = w[o].q || []).push(arguments) };
				js = d.createElement(s), fjs = d.getElementsByTagName(s)[0];
				js.id = '1-43c2cd496486bcc27217c3e790fb4088'; js.dataset.a = 'hnutibrontosaurus'; js.src = f; js.async = 1; fjs.parentNode.insertBefore(js, fjs);
			}(window, document, 'script', 'ecmwidget', 'https://d70shl7vidtft.cloudfront.net/widget.js'));
		</script>
		<div id="f-1-43c2cd496486bcc27217c3e790fb4088"></div>
		<script>
			function runWhenAvailable(selector, callback) {
				const observer = new MutationObserver((mutations, observerInstance) => {
					for (const mutation of mutations) {
						if (mutation.type === 'childList') {
							const element = document.querySelector(selector);
							if (element) {
								callback(element);
								observerInstance.disconnect();
								return;
							}
						}
					}
				});
				observer.observe(document.body, { subtree: true, childList: true });
			}

			runWhenAvailable('.ec-v-form-submit button', (el) => {
				el.classList.add('action', 'action--primary', 'action--lg');
			});
		</script>
	</div>

	<div class="newsletter__socials">
		<a class="newsletter__instagramLink" href="https://www.instagram.com/hnutibrontosaurus" rel="noopener noreferrer" target="_blank" aria-label="Instagram Hnutí Brontosaurus"></a>
		<a class="newsletter__facebookLink" href="https://www.facebook.com/hnutibrontosaurus" rel="noopener noreferrer" target="_blank" aria-label="Facebook Hnutí Brontosaurus"></a>
		<a class="newsletter__youtubeLink" href="https://www.youtube.com/channel/UCiytQ5b-4GzZbYWDVMwNJ7A" rel="noopener noreferrer" target="_blank" aria-label="Youtube Hnutí Brontosaurus"></a>
	</div>

</div>

<div class="footer-wrapper" id="navigace">
	<footer class="footer">
		<nav class="footer__navigation" role="navigation">
			<?php // three menus are used so that there is clear distinction what will render in which column ?>
			<?php // otherwise we would have to rely on layout which wouldn't produce the desired result ?>

			<?php wp_nav_menu([
				'theme_location' => 'footer-left',
				'container' => false,
				'depth' => 1,
			]); ?>

			<?php wp_nav_menu([
				'theme_location' => 'footer-center',
				'container' => false,
				'depth' => 1,
			]); ?>

			<?php wp_nav_menu([
				'theme_location' => 'footer-right',
				'container' => false,
				'depth' => 1,
			]); ?>
		</nav>

		<div class="footer__bottom">
			<a class="footer__logo" href="<?php echo get_home_url(); ?>" aria-label="Hnutí Brontosaurus"></a>

			<div class="footer__searchForm searchForm__gcseRoot">
				<div
					class="gcse-searchbox-only"
					data-resultsUrl="/vysledky-vyhledavani"
					data-queryParameterName="q"
					data-enableAutoComplete="false"
				></div>

				<script type="text/javascript">
					window.addEventListener('load', function () {
						document.getElementById('gsc-i-id2').placeholder = 'Prohledat web';
						const searchButtonEl = document.getElementById('___gcse_1').querySelector('button.gsc-search-button');
						searchButtonEl.classList.add('action', 'action--primary', 'action--lg');
						searchButtonEl.innerHTML = 'Hledat';
					});
				</script>
			</div>
		</div>
	</footer>
</div>
</div>
</body>
</html>
