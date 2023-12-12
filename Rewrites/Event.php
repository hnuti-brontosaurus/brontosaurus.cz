<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\Rewrites;

use HnutiBrontosaurus\Theme\UI\Event\EventController;
use function add_rewrite_rule;
use function array_push;
use function sprintf;


final class Event
{

	public static function rewriteRule(): void
	{
		add_rewrite_rule(
			sprintf('^%s/([\d]+)',
				EventController::PAGE_SLUG,
			),
			sprintf('index.php?pagename=%s&%s=$matches[1]',
				EventController::PAGE_SLUG,
				EventController::PARAM_EVENT_ID,
			),
			'top',
		);
	}

	public static function queryVars(array $vars): void
	{
		array_push($vars, EventController::PARAM_EVENT_ID);
	}

}
