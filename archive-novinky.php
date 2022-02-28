<?php declare(strict_types = 1);

use HnutiBrontosaurus\Theme\DataContainers\NewsPost;


require_once __DIR__ . '/bootstrap.php';


$configuration = hb_getConfiguration();
set_query_var('hb_enableTracking', $configuration->get('enableTracking')); // temporary pass setting to template (better solution is to use WP database to store these things)
get_header();

$dateForHumans = $configuration->get('dateFormat:human');

?>
<main role="main">
	<article class="news">
		<h1>
			Co&nbsp;je nového
		</h1>

		<?php if (have_posts()): ?>
			<ul class="news__list">
				<?php while(have_posts()): ?>
					<?php
						the_post();
						$post = get_post();
						if ($post === null) break;
						$article = NewsPost::from($post);
					?>
					<li class="news__item">
						<a class="news__link" href="<?php echo get_permalink($article->id) ?>">
							<h2 class="news__item-heading">
								<?php echo $article->title ?>
							</h2>

							<time class="news__item-date">
								<?php echo $article->date->format($dateForHumans) ?>
							</time>

							<div class="news__item-content">
								<?php if ($article->hasCoverImage): ?>
								<div class="news__item-coverImage">
									<img alt="" src="<?php echo $article->coverImage ?>">
								</div>
								<?php endif; ?>

								<div class="news__item-perex">
									<?php echo $article->perex ?>
								</div>

								<div class="news__item-moreLink">
									číst dále&hellip;
								</div>
							</div>
						</a>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php
				the_posts_pagination([
					'before_page_number' => '',
					'mid_size'           => 5,
					'prev_text'          => '‹ <span class="nav-prev-text">předchozí</span>',
					'next_text'          => '<span class="nav-next-text">další</span> ›',
				]);
			?>

			<div class="news__clear"></div>
		<?php else: ?>
			<div class="news__list news__list--empty">
				Momentálně tu nic nemáme&hellip;
			</div>
		<?php endif; ?>
	</article>
</main>
<?php

get_footer();
