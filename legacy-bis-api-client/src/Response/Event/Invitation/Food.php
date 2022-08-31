<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Invitation;

use Grifart\Enum\AutoInstances;
use Grifart\Enum\Enum;


/**
 * @method static Food NOT_LISTED()
 * @method static Food CHOOSEABLE()
 * @method static Food VEGETARIAN()
 * @method static Food NON_VEGETARIAN()
 */
final class Food extends Enum
{
	use AutoInstances;

	protected const NOT_LISTED = -1;
	protected const CHOOSEABLE = 0;
	protected const VEGETARIAN = 1;
	protected const NON_VEGETARIAN = 2;
}
