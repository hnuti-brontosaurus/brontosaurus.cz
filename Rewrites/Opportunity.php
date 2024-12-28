<?php

namespace HnutiBrontosaurus\Theme\Rewrites;

use function add_rewrite_rule;
use function array_push;
use function sprintf;


final class Opportunity
{
	public const HB_OPPORTUNITY_ID = 'opportunityId';

	public static function rewriteRule(): void
	{
		add_rewrite_rule(
			'^zapoj-se/prilezitost/([\d]+)',
			sprintf('index.php?pagename=prilezitost&%s=$matches[1]', self::HB_OPPORTUNITY_ID),
			'top',
		);
	}

	public static function queryVars(array &$vars): void
	{
		array_push($vars, self::HB_OPPORTUNITY_ID);
	}

}
