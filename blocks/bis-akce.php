<?php

use HnutiBrontosaurus\BisClient\BisClientFactory;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Category;
use HnutiBrontosaurus\BisClient\Event\Group;
use HnutiBrontosaurus\BisClient\Event\IntendedFor;
use HnutiBrontosaurus\BisClient\Event\Program;
use HnutiBrontosaurus\BisClient\Event\Request\EventParameters;
use HnutiBrontosaurus\BisClient\Event\Request\Period;
use HnutiBrontosaurus\BisClient\Event\Request\Region;
use HnutiBrontosaurus\BisClient\Event\Tag;

function brontosaurus_register_bis_events_block(): void
{
	register_block_type('brontosaurus/bis-akce', [
		'api_version' => 3,
		'attributes' => [
			'limit' => [
				'type' => 'number',
				'default' => 9,
			],
			'tag' => [
				'type' => 'string',
				'default' => '',
			],
			'program' => [
				'type' => 'string',
				'default' => '',
			],
			'intendedFor' => [
				'type' => 'string',
				'default' => '',
			],
			'category' => [
				'type' => 'string',
				'default' => '',
			],
			'group' => [
				'type' => 'string',
				'default' => '',
			],
			'region' => [
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
		? min(15, max(3, (int) $attributes['limit']))
		: 9;

	$tag = Tag::tryFrom(brontosaurus_bis_string_attribute($attributes, 'tag'));
	$program = Program::tryFrom(brontosaurus_bis_string_attribute($attributes, 'program'));
	$intendedFor = IntendedFor::tryFrom(brontosaurus_bis_string_attribute($attributes, 'intendedFor'));
	$category = Category::tryFrom(brontosaurus_bis_string_attribute($attributes, 'category'));
	$group = Group::tryFrom(brontosaurus_bis_string_attribute($attributes, 'group'));
	$regionValue = brontosaurus_bis_string_attribute($attributes, 'region');
	$region = ctype_digit($regionValue) ? Region::tryFrom((int) $regionValue) : null;

	$cacheKey = 'brontosaurus_bis_akce_' . md5(implode(':', [
		$tag?->value ?? '',
		$program?->value ?? '',
		$intendedFor?->value ?? '',
		$category?->value ?? '',
		$group?->value ?? '',
		$region?->value ?? '',
		(string) $limit,
	]));
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
	if ($program !== null) {
		$parameters->setProgram($program);
	}
	if ($intendedFor !== null) {
		$parameters->setIntendedFor($intendedFor);
	}
	if ($category !== null) {
		$parameters->setCategory($category);
	}
	if ($group !== null) {
		$parameters->setGroup($group);
	}
	if ($region !== null) {
		$parameters->setRegion($region);
	}

	try {
		$events = (new BisClientFactory('https://bis.brontosaurus.cz/api'))
			->create()
			->getEvents($parameters);
	} catch (ConnectionToBisFailed $exception) {
		error_log('Unable to load events from BIS: ' . $exception->getMessage());
		return '<p class="bis-akce__message">Omlouváme se, akce se teď nepodařilo načíst. Zkuste to prosím později.</p>';
	}

	if ($events === null || $events === []) {
		$markup = '<p class="bis-akce__message">Momentálně nejsou vypsané žádné akce.</p>';
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

		$items .= '<li class="bis-akce__item">';
		if ($coverPhotoPath !== null) {
			$items .= sprintf(
				'<a class="bis-akce__image-link" href="%s"><img class="bis-akce__image" src="%s" alt="" loading="lazy"></a>',
				esc_url($eventUrl),
				esc_url($coverPhotoPath),
			);
		}
		$items .= '<div class="bis-akce__content">';
		$items .= sprintf(
			'<h3 class="bis-akce__title"><a href="%s">%s</a></h3>',
			esc_url($eventUrl),
			esc_html($event->name),
		);
		$items .= sprintf('<p class="bis-akce__date">%s</p>', esc_html($date));
		if ($location !== '') {
			$items .= sprintf('<p class="bis-akce__location">%s</p>', esc_html($location));
		}
		$items .= '</div></li>';
	}

	$markup = '<ul class="bis-akce">' . $items . '</ul>';
	set_transient($cacheKey, $markup, 5 * MINUTE_IN_SECONDS);

	return $markup;
}

function brontosaurus_bis_string_attribute(array $attributes, string $name): string
{
	return isset($attributes[$name]) && is_string($attributes[$name])
		? $attributes[$name]
		: '';
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
