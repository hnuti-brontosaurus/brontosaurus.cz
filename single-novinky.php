<?php declare(strict_types = 1);

use HnutiBrontosaurus\Theme\DataContainers\NewsPost;


require_once __DIR__ . '/bootstrap.php';


$configuration = hb_getConfiguration();
set_query_var('hb_enableTracking', $configuration->get('enableTracking')); // temporary pass setting to template (better solution is to use WP database to store these things)
get_header();

$dateForHumans = $configuration->get('dateFormat:human');

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
