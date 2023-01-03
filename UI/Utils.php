<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI;

use HnutiBrontosaurus\Theme\UI\DataContainers\Events\EventDC;
use Latte\Engine;


final class Utils
{

	public static function registerFormatPhoneNumberLatteFilter(Engine $latteEngine): void
	{
		$latteEngine->addFilter('formatPhoneNumber', function (string $phoneNumber): string
		{
			// todo use brick/phone instead?
			switch (\mb_strlen($phoneNumber)) {
				case 9: // 123456789
					return \sprintf('%s %s %s', \mb_substr($phoneNumber, 0, 3), \mb_substr($phoneNumber, 3, 3), \mb_substr($phoneNumber, 6, 3));
				case 11: // 123 456 789
					return $phoneNumber;
				case 13: // +420123456789
					return \sprintf('%s %s %s %s', \mb_substr($phoneNumber, 0, 4), \mb_substr($phoneNumber, 4, 3), \mb_substr($phoneNumber, 7, 3), \mb_substr($phoneNumber, 10, 3));
				case 16: // +420 123 456 789
					return $phoneNumber;
				case 14: // 00420123456789
					return \sprintf('+%s %s %s %s', \mb_substr($phoneNumber, 2, 3), \mb_substr($phoneNumber, 5, 3), \mb_substr($phoneNumber, 8, 3), \mb_substr($phoneNumber, 11, 3));
				case 17: // 00420 123 456 789
				case 18: // 00 420 123 456 789
					return \str_replace(['00 ', '00'], '+', $phoneNumber);
			}

			return $phoneNumber; // fallback any other format
		});
	}


	public static function registerTypeByDayCountLatteFilter(Engine $latteEngine): void
	{
		$latteEngine->addFilter('typeByDayCount', function (int $dayCount): string
		{
			return match (EventDC::resolveDurationCategory($dayCount))
			{
				EventDC::DURATION_CATEGORY_ONE_DAY => 'jednodenní',
				EventDC::DURATION_CATEGORY_WEEKEND => 'víkendovka',
				default => 'dlouhodobá',
			};
		});
	}


	/**
	 * Inspired with https://zlml.cz/vlna-na-webu
	 * @param string $input
	 * @return string
	 */
	public static function handleNonBreakingSpaces(string $input): string
	{
		return \preg_replace('/(\s)([ksvzaiou])\s/i', "$1$2\xc2\xa0", $input); // &nbsp; === \xc2\xa0
	}

}
