<?php

namespace HnutiBrontosaurus\Theme;

use HnutiBrontosaurus\Theme\DataContainers\NewsPost;
use function get_footer;
use function get_header;
use function get_permalink;
use function get_post;
use function have_posts;
use function set_query_var;
use function the_posts_pagination;


/** @var Container $hb_container defined in functions.php */

set_query_var('hb_enableTracking', $hb_container->getEnableTracking()); // temporary pass setting to template (better solution is to use WP database to store these things)
get_header();

$dateForHumans = $hb_container->getDateFormatForHuman();

?><main role="main">
	<article class="news hb-mi-auto">
		<h1>
			Co je nového
		</h1>

		<?php if (have_posts()): ?>
			<ul class="hb-lst-n">
				<?php while(have_posts()): ?>
					<?php
						the_post();
						$post = get_post();
						if ($post === null) break;
						$article = NewsPost::from($post);
					?>
					<li class="hb-mbe-5 hb-c-r">
						<a class="hb-td-n" href="<?php echo get_permalink($article->id) ?>">
							<h2 class="news__item-heading hb-ta-l hb-mbe-0">
								<?php echo $article->title ?>
							</h2>

							<time class="news__item-date hb-d-b hb-mbe-2 hb-text-framing">
								<?php echo $article->date->format($dateForHumans) ?>
							</time>

							<div class="news__item-content hb-wp-content">
								<?php if ($article->hasCoverImage): ?>
								<div class="news__item-coverImage">
									<img class="hb-br" alt="" src="<?php echo $article->coverImage ?>">
								</div>
								<?php endif; ?>

								<div>
									<?php echo $article->perex ?>
								</div>

								<div class="news__item-moreLink hb-mbs-3 hb-text-framing">
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

			<div class="hb-c-r"></div>
		<?php else: ?>
			<div class="hb-lg-ta-c">
				Momentálně tu nic nemáme&hellip;
			</div>
		<?php endif; ?>
	</article>
</main>
<?php

get_footer();
