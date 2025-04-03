<?php function hb_story(stdClass $story) { ?>
<a class="hb-event" href="<?php echo $story->link ?>">
	<div class="hb-event__imageWrapper">
		<img alt="" class="hb-event__image<?php if ( ! $story->hasThumbnail): ?> hb-event__image--noThumbnail<?php endif; ?>" data-src="<?php if ($story->hasThumbnail): ?><?php echo $story->thumbnail ?><?php else: ?>https://brontosaurus.cz/wp-content/uploads/2024/12/logo-hb-brontosaurus.svg<?php endif; ?>">
		<!-- for search engines -->
		<noscript>
			<img alt="" class="hb-event__image<?php if ( ! $story->hasThumbnail): ?> hb-event__image--noThumbnail<?php endif; ?>" src="<?php if ($story->hasThumbnail): ?><?php echo $story->thumbnail ?><?php else: ?>https://brontosaurus.cz/wp-content/uploads/2024/12/logo-hb-brontosaurus.svg<?php endif; ?>">
		</noscript>
	</div>

	<header class="hb-event__header">
		<h4 class="hb-event__heading">
		<?php echo $story->title ?>
		</h4>

		<?php if ($story->location): ?>
		<div class="hb-event__meta">
			<span class="hb-event__place" title="Místo">
				<?php echo $story->location ?>
			</span>
		</div>
		<?php endif; ?>
	</header>

	<div class="hb-event__excerpt">
		<?php echo $story->excerpt ?>
	</div>
</a>
<?php }

// todo: use some WP way of obtaining param
$all = filter_input( INPUT_GET, 'vsechny' ) ?? null;
$shouldListAll = $all === '';

$posts = get_posts(['post_type' => 'pribehy-nadseni', 'numberposts' => -1]);

$displayOnLoad = $shouldListAll ? null : 3;
$stories = array_map(function (WP_Post $post) {
	$thumbnail = get_the_post_thumbnail_url($post);
	$thumbnail = $thumbnail === false ? null : $thumbnail; // convert false to null which makes more sense

	$customs = get_post_custom($post->ID);
	$location = count($customs) > 0 && array_key_exists('pribehy-nadseni_location', $customs) && array_key_exists(0, $customs['pribehy-nadseni_location'])
		? $customs['pribehy-nadseni_location'][0]
		: null;

	return (object) [
		'title' => $post->post_title,
		'location' => $location,
		'excerpt' => $post->post_excerpt,
		'hasThumbnail' => $thumbnail !== null,
		'thumbnail' => $thumbnail,
		'link' => get_page_link($post),
	];
}, $posts);

?><main role="main">
	<article class="hb-mbe-6">
		<h1>
			Příběhy nadšení
		</h1>

		<p class="hb-block-text hb-mbe-4">
			Už je to přes 50 let, kdy v roce 1974 vzbudila Akce Brontosaurus obrovskou vlnu nadšení
			pro pomoc přírodě a památkám. Desetitisíce lidí se díky ní pustily do sázení stromů,
			oprav hradů, úklidů koryt řek, kosení chráněných luk nebo čištění studánek.
			U příležitosti výročí půlstoletí Brontosaura představujeme vybrané příběhy úspěšných dobrovolnických projektů.
		</p>

		<div class="hb-eventList hb-mbe-4">
			<div class="hb-eventList__grid">
				<?php 
				$counter = 1;
				foreach ($stories as $story) {
                	hb_story($story);
					if ($displayOnLoad !== null && $counter === $displayOnLoad) {
						break;
					}
					$counter++;
				} 
				?>
			</div>
			<?php if ($displayOnLoad !== null && count($stories) > $displayOnLoad): ?>
				<div class="hb-expandable">
					<button class="hb-eventList__moreLink button button--customization" type="button" data-hb-expandable-toggler data-hb-expandable-toggler-remove-on-expand>
						Zobrazit další
					</button>

					<div class="hb-eventList__grid hb-eventList__grid--collapse" data-hb-expandable-content>
						<?php 
						$counter = 1;
						foreach ($stories as $story) {
							if ($counter <= $displayOnLoad) {
								$counter++;
								continue;
							}
							hb_story($story);
						}
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<p class="hb-block-text hb-mbe-4">
			Tyto příběhy jsou jen malým dílem obrovského úsilí o zdravé životní prostředí,
			respekt k přírodě a udržitelný život. Mohou nás však inspirovat k aktivitě
			a přinášet naději a důvěru v budoucnost.

			Koukni na další příběhy ve videích níže nebo si je poslechni v našem
			<a href="https://open.spotify.com/show/2FUTJzuAiTbYlTVXSsmT5F?si=d60021af65914673" rel="noopener noreferrer" target="_blank">podcastu Příběhy nadšení</a>.
			Nastartujme společně <a href="https://dalsich50.cz" rel="noopener noreferrer" target="_blank">Dalších 50 let</a>
			péče o přírodu, památky a lidi s Brontosaurem!
		</p>

		<div class="hb-mw-50 [ hb-d-f hb-fd-c hb-lg-fd-r hb-g-4 hb-lg-g-3 ][ hb-mi-auto hb-mbs-6 hb-mbe-5 hb-lg-mbe-4 ]">
			<iframe class="hb-d-b hb-is-100 hb-ar-169 hb-br" src="https://www.youtube.com/embed/6RCZ2E5S9qo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

			<iframe class="hb-d-b hb-is-100 hb-ar-169 hb-br" src="https://www.youtube.com/embed/DM-BiimluBw" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div>

		<div class="hb-ta-c hb-mbe-5">
			<p class="hb-mbe-3">
				Dává ti smysl, co děláme?
				Adoptuj Brontosaura a podpoř naši péči přírodu a památky.
			</p>

			<a class="[ hb-action hb-action--primary hb-action--lg ][ hb-d-b hb-mw-mc hb-mi-auto ]" href="/podpor-nas">Chci adoptovat Brontosaura</a>
		</div>
	</article>
</main>
