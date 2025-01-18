<?php

namespace HnutiBrontosaurus\Theme\Rewrites;

use function add_rewrite_rule;
use function array_push;
use function sprintf;


final class Event
{

	public static function rewriteRule(): void
	{
		add_rewrite_rule('^akce/([\d]+)', 'index.php?pagename=akce&eventId=$matches[1]', 'top');
	}

	public static function queryVars(array &$vars): void
	{
		array_push($vars, 'eventId');
	}

}
