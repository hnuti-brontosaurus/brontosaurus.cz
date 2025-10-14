<?php

use Brick\DateTime\LocalDate;
use HnutiBrontosaurus\BisClient\Opportunity\Category;

function hb_dateSpan(LocalDate $start, LocalDate $end, string $dateFormat): string
{
	$start = $start->toNativeDateTimeImmutable();
	$end = $end->toNativeDateTimeImmutable();
	$dateSpan_untilPart = $end->format($dateFormat);

	$onlyOneDay = $start->format('j') === $end->format('j');
	if ($onlyOneDay) {
		return $dateSpan_untilPart;
	}

	$inSameMonth = $start->format('n') === $end->format('n');
	$inSameYear = $start->format('Y') === $end->format('Y');

	$dateSpan_fromPart = $start->format(sprintf('j.%s%s',
		( ! $inSameMonth || ! $inSameYear) ? ' n.' : '',
		( ! $inSameYear) ? ' Y' : ''
	));

	// Czech language rules say that in case of multi-word date span there should be a space around the dash (@see http://prirucka.ujc.cas.cz/?id=810)
	$optionalSpace = '';
	if ( ! $inSameMonth) {
		$optionalSpace = ' ';
	}

	return $dateSpan_fromPart . sprintf('%s–%s', $optionalSpace, $optionalSpace) . $dateSpan_untilPart;
}


function hb_opportunityCategoryToString(Category $category): string
{
	return match ($category) {
		Category::ORGANIZING() => 'organizování akcí',
		Category::COLLABORATION() => 'spolupráce',
		Category::LOCATION_HELP() => 'pomoc lokalitě',
	};
}


/**
 * Inspired with https://zlml.cz/vlna-na-webu
 */
function hb_handleNonBreakingSpaces(string $input): string
{
	return preg_replace('/(\s)([ksvzaiou])\s/i', "$1$2\xc2\xa0", $input); // \xc2\xa0 is a non breaking space
}


function hb_strip_tags(string $input): string
{
	$s = strip_tags($input);
	$s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	return $s;
}


function hb_truncate(string $string, int $maxLength, string $suffix = '…')
{
	// doesn't need truncation
	if (mb_strlen($string) <= $maxLength) {
		return $string;
	}

	return mb_substr($string, 0, $maxLength - mb_strlen($suffix)) . $suffix;
}