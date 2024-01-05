<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme;


use Brick\DateTime\LocalDate;
use HnutiBrontosaurus\BisClient\Opportunity\Category;
use function get_permalink;
use function get_posts;
use function reset;


function getLinkFor(string $slug): string
{
	$posts = get_posts(['name' => $slug, 'post_type' => 'page', 'posts_per_page' => 1]);
	$post = reset($posts);
	if ($post === false) {
		throw new \RuntimeException("$slug does not exist");
	}

	return get_permalink($post->ID);
}


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

	$dateSpan_fromPart = $start->format(\sprintf('j.%s%s',
		( ! $inSameMonth || ! $inSameYear) ? ' n.' : '',
		( ! $inSameYear) ? ' Y' : ''
	));

	// Czech language rules say that in case of multi-word date span there should be a space around the dash (@see http://prirucka.ujc.cas.cz/?id=810)
	$optionalSpace = '';
	if ( ! $inSameMonth) {
		$optionalSpace = ' ';
	}

	return $dateSpan_fromPart . \sprintf('%s–%s', $optionalSpace, $optionalSpace) . $dateSpan_untilPart;
}


function hb_opportunityCategoryToString(Category $category): string
{
	return match ($category) {
		Category::ORGANIZING() => 'organizování akcí',
		Category::COLLABORATION() => 'spolupráce',
		Category::LOCATION_HELP() => 'pomoc lokalitě',
	};
}
