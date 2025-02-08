<?php

namespace HnutiBrontosaurus\Theme;

use Tracy\Debugger;
use WP_Post;
use function apply_filters;
use function get_footer;
use function get_header;
use function get_post;
use function get_template_part;
use function ob_end_clean;
use function ob_get_contents;
use function ob_start;
use function set_query_var;


/** @var Container $hb_container defined in functions.php */


(function (?WP_Post $post, Container $hb_container) {
	// tracy
	Debugger::$logDirectory = __DIR__ . '/log';
	Debugger::$strictMode = true;
	Debugger::enable( ! $hb_container->getDebugMode());

	// app

	set_query_var('hb_enableTracking', $hb_container->getEnableTracking()); // temporary pass setting to template (better solution is to use WP database to store these things)
	set_query_var('hb_container', $hb_container); // query vars are exported with extract() in template parts

	// use template part if available
	if ($post !== null) {
		ob_start();
		if ($post->post_content !== '') {
			// todo use the_post() instead, but it did not work in current flow (with the controller things probably)
			echo '<h1>'.$post->post_title.'</h1>';
			echo '<div class="content__dynamic">' . apply_filters('the_content', $post->post_content) . '</div>';
			$result = null;
		} else {
			$result = get_template_part('template-parts/content/content', $post->post_name);
		}
		$content = ob_get_contents();
		ob_end_clean();
		$success = $result !== false; // get_template_part() doesn't return true in case of success
		if ($success) {
			get_header();
			echo $content;
			get_footer();
			return;
		}
	}

	if (isset($_GET['preview']) && $_GET['preview'] === 'true') {
		get_header();
		get_template_part('template-parts/content/content', 'preview');
		get_footer();
		return;
	}

	get_header();
	get_template_part('template-parts/content/content', 'error');
	get_footer();
})(get_post(), $hb_container);
