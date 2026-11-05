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

function brontosaurus_register_bis_akce_block(): void
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
			'filters' => [
				'type' => 'array',
				'default' => [],
			],
		],
		'render_callback' => 'brontosaurus_render_bis_akce_block',
	]);
}
add_action('init', 'brontosaurus_register_bis_akce_block');

function brontosaurus_render_bis_akce_block(array $attributes): string
{
	$limit = isset($attributes['limit']) && is_numeric($attributes['limit'])
		? min(15, max(3, (int) $attributes['limit']))
		: 9;

	$views = [
		[
			'label' => 'vše',
			'filters' => $attributes,
		],
	];
	$customFilters = isset($attributes['filters']) && is_array($attributes['filters'])
		? $attributes['filters']
		: [];
	foreach ($customFilters as $filter) {
		if (!is_array($filter)) {
			continue;
		}

		$views[] = [
			'label' => isset($filter['name']) && is_string($filter['name']) && $filter['name'] !== ''
				? $filter['name']
				: 'Filtr',
			'filters' => $filter,
		];
	}

	$markup = '<div class="bis-akce"><div class="bis-akce__tabs" role="group" aria-label="Přepnout výpis akcí">';
	$panels = '';
	foreach ($views as $index => $view) {
		$panelId = wp_unique_id('bis-akce-view-');
		$markup .= sprintf(
			'<button class="bis-akce__tab" type="button" aria-controls="%s" aria-pressed="%s" data-bis-akce-view="%s">%s</button>',
			esc_attr($panelId),
			$index === 0 ? 'true' : 'false',
			esc_attr($panelId),
			esc_html($view['label']),
		);
		$panels .= sprintf(
			'<div class="bis-akce__view" id="%s" data-bis-akce-panel%s>%s</div>',
			esc_attr($panelId),
			$index === 0 ? '' : ' hidden',
			brontosaurus_bis_events_markup($view['filters'], $limit),
		);
	}

	return $markup . '</div><div class="bis-akce__views">' . $panels . '</div></div>';
}

function brontosaurus_bis_events_markup(array $attributes, int $limit): string
{
	$tags = array_values(array_filter(array_map(
		static fn(string $value): ?Tag => Tag::tryFrom($value),
		brontosaurus_bis_selected_values($attributes, 'tag'),
	)));
	$programs = array_values(array_filter(array_map(
		static fn(string $value): ?Program => Program::tryFrom($value),
		brontosaurus_bis_selected_values($attributes, 'program'),
	)));
	$intendedForValues = array_values(array_filter(array_map(
		static fn(string $value): ?IntendedFor => IntendedFor::tryFrom($value),
		brontosaurus_bis_selected_values($attributes, 'intendedFor'),
	)));
	$categories = array_values(array_filter(array_map(
		static fn(string $value): ?Category => Category::tryFrom($value),
		brontosaurus_bis_selected_values($attributes, 'category'),
	)));
	$groups = array_values(array_filter(array_map(
		static fn(string $value): ?Group => Group::tryFrom($value),
		brontosaurus_bis_selected_values($attributes, 'group'),
	)));
	$regions = array_values(array_filter(array_map(
		static fn(string $value): ?Region => ctype_digit($value) ? Region::tryFrom((int) $value) : null,
		brontosaurus_bis_selected_values($attributes, 'region'),
	)));

	$cacheFilters = array_map(
		static function (array $values): string {
			$values = array_map(static fn(BackedEnum $value): string => (string) $value->value, $values);
			sort($values);
			return implode(',', $values);
		},
		[$tags, $programs, $intendedForValues, $categories, $groups, $regions],
	);

	$cacheKey = 'brontosaurus_bis_akce_' . md5(implode(':', [...$cacheFilters, (string) $limit]));
	$cachedMarkup = get_transient($cacheKey);
	if (is_string($cachedMarkup)) {
		return $cachedMarkup;
	}

	$parameters = (new EventParameters())
		->setPeriod(Period::FUTURE_ONLY)
		->setLimit($limit)
		->orderByStartDate();

	if ($tags !== []) {
		$parameters->setTags($tags);
	}
	if ($programs !== []) {
		$parameters->setPrograms($programs);
	}
	if ($intendedForValues !== []) {
		$parameters->setMultipleIntendedFor($intendedForValues);
	}
	if ($categories !== []) {
		$parameters->setCategories($categories);
	}
	if ($groups !== []) {
		$parameters->setGroups($groups);
	}
	if ($regions !== []) {
		$parameters->setRegions($regions);
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

	$markup = '<ul class="bis-akce__list">' . $items . '</ul>';
	set_transient($cacheKey, $markup, 5 * MINUTE_IN_SECONDS);

	return $markup;
}

function brontosaurus_bis_selected_values(array $attributes, string $name): array
{
	$value = $attributes[$name] ?? '';
	if (is_array($value)) {
		return array_values(array_filter($value, 'is_string'));
	}
	if (!is_string($value)) {
		return [];
	}

	return array_values(array_filter(explode(',', $value), static fn(string $item): bool => $item !== ''));
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
