<?php

use HnutiBrontosaurus\BisClient\BisClientFactory;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\BisClient\Event\Request\Period;
use HnutiBrontosaurus\BisClient\Event\Tag;

function brontosaurus_register_bis_events_block(): void
{
	register_block_type('brontosaurus/bis-events', [
		'api_version' => 3,
		'attributes' => [
			'limit' => [
				'type' => 'number',
				'default' => 6,
			],
			'tag' => [
				'type' => 'string',
				'default' => '',
			],
		],
		'render_callback' => 'brontosaurus_render_bis_events_block',
	]);
}
add_action('init', 'brontosaurus_register_bis_events_block');

function brontosaurus_render_bis_events_block(array $attributes): string
{
	$limit = isset($attributes['limit']) && is_numeric($attributes['limit'])
		? min(12, max(1, (int) $attributes['limit']))
		: 6;

	$tagValue = isset($attributes['tag']) && is_string($attributes['tag'])
		? $attributes['tag']
		: '';
	$tag = $tagValue !== '' ? Tag::tryFrom($tagValue) : null;
	if ($tagValue !== '' && $tag === null) {
		$tagValue = '';
	}

	$cacheKey = 'brontosaurus_bis_events_' . md5($tagValue . ':' . $limit);
	$cachedMarkup = get_transient($cacheKey);
	if (is_string($cachedMarkup)) {
		return $cachedMarkup;
	}

	$parameters = (new EventParameters())
		->setPeriod(Period::FUTURE_ONLY)
		->setLimit($limit)
		->orderByStartDate();

	if ($tag !== null) {
		$parameters->setTag($tag);
	}

	try {
		$events = (new BisClientFactory('https://bis.brontosaurus.cz/api'))
			->create()
			->getEvents($parameters);
	} catch (ConnectionToBisFailed $exception) {
		error_log('Unable to load events from BIS: ' . $exception->getMessage());
		return '<p class="bis-events__message">Omlouváme se, akce se teď nepodařilo načíst. Zkuste to prosím později.</p>';
	}

	if ($events === null || $events === []) {
		$markup = '<p class="bis-events__message">Momentálně nejsou vypsané žádné akce.</p>';
		set_transient($cacheKey, $markup, 5 * MINUTE_IN_SECONDS);
		return $markup;
	}

	$items = '';
	foreach ($events as $event) {
		$startDate = $event->startDate;
		$endDate = $event->endDate;
		$date = brontosaurus_format_bis_event_date($startDate, $endDate);
		$location = $event->location->name;
		$coverPhotoPath = $event->coverPhotoPath?->mediumSizePath;
		$eventUrl = sprintf('https://bis.brontosaurus.cz/akce/%d/', $event->id);

		$items .= '<li class="bis-events__item">';
		if ($coverPhotoPath !== null) {
			$items .= sprintf(
				'<a class="bis-events__image-link" href="%s"><img class="bis-events__image" src="%s" alt="" loading="lazy"></a>',
				esc_url($eventUrl),
				esc_url($coverPhotoPath),
			);
		}
		$items .= '<div class="bis-events__content">';
		$items .= sprintf(
			'<h3 class="bis-events__title"><a href="%s">%s</a></h3>',
			esc_url($eventUrl),
			esc_html($event->name),
		);
		$items .= sprintf('<p class="bis-events__date">%s</p>', esc_html($date));
		if ($location !== '') {
			$items .= sprintf('<p class="bis-events__location">%s</p>', esc_html($location));
		}
		$items .= '</div></li>';
	}

	$markup = '<ul class="bis-events">' . $items . '</ul>';
	set_transient($cacheKey, $markup, 5 * MINUTE_IN_SECONDS);

	return $markup;
}

function brontosaurus_format_bis_event_date(DateTimeInterface $startDate, DateTimeInterface $endDate): string
{
	if ($startDate->format('Ymd') === $endDate->format('Ymd')) {
		return $startDate->format('j. n. Y');
	}

	$startFormat = $startDate->format('Y') === $endDate->format('Y')
		? 'j. n.'
		: 'j. n. Y';

	return $startDate->format($startFormat) . ' – ' . $endDate->format('j. n. Y');
}
