<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;

use HnutiBrontosaurus\Theme\DataContainers\NewsPost;
use function get_header;
use function get_post;
use function set_query_var;


/** @var Container $hb_container defined in functions.php */

set_query_var('hb_enableTracking', $hb_container->getEnableTracking()); // temporary pass setting to template (better solution is to use WP database to store these things)
get_header();

$dateForHumans = $hb_container->getDateFormatForHuman();

$post = get_post();
if ($post === null) {
	// todo
}
$article = NewsPost::from($post);

?>
<main role="main">
	<article class="news news--single">
		<h1 class="news__item-heading">
			<?php echo $article->title ?>
		</h1>

		<time class="news__item-date">
			<?php echo $article->date->format($dateForHumans) ?>
		</time>

		<div class="news__item-content">
			<?php if ($article->hasCoverImage): ?>
				<div class="news__item-coverImage">
					<img alt="" src="<?php echo $article->coverImage ?>">
				</div>
			<?php endif; ?>

			<div>
				<?php echo $article->content ?>
			</div>
		</div>

		<div class="news__clear"></div>
	</article>
</main>
<?php

get_footer();
