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

function brontosaurus_register_bis_akce_rest_route(): void
{
	register_rest_route('brontosaurus/v1', '/events', [
		'methods' => 'POST',
		'callback' => 'brontosaurus_load_bis_events',
		'permission_callback' => '__return_true',
	]);
}
add_action('rest_api_init', 'brontosaurus_register_bis_akce_rest_route');

function brontosaurus_load_bis_events(WP_REST_Request $request): WP_REST_Response
{
	$providedFilters = $request->get_param('filters');
	if (!is_array($providedFilters)) {
		return new WP_REST_Response(['message' => 'Neplatné nastavení filtru.'], 400);
	}

	$filterNames = ['tag', 'program', 'intendedFor', 'category', 'group', 'region'];
	$filters = [];
	foreach ($filterNames as $name) {
		$value = $providedFilters[$name] ?? null;
		if (is_string($value)) {
			$filters[$name] = $value;
		} elseif (is_array($value)) {
			$filters[$name] = array_values(array_filter($value, 'is_string'));
		}
	}

	$providedLimit = $request->get_param('limit');
	$limit = is_numeric($providedLimit)
		? min(15, max(3, (int) $providedLimit))
		: 9;
	$providedOffset = $request->get_param('offset');
	$offset = is_numeric($providedOffset)
		? max(0, (int) $providedOffset)
		: 0;

	return new WP_REST_Response([
		'markup' => brontosaurus_bis_events_markup($filters, $limit, $offset),
	]);
}

function brontosaurus_render_bis_akce_block(array $attributes): string
{
	$limit = isset($attributes['limit']) && is_numeric($attributes['limit'])
		? min(15, max(3, (int) $attributes['limit']))
		: 9;

	$views = [
		[
			'label' => 'vše',
			'query' => '',
			'filters' => $attributes,
		],
	];
	$usedFilterSlugs = [];
	$customFilters = isset($attributes['filters']) && is_array($attributes['filters'])
		? $attributes['filters']
		: [];
	foreach ($customFilters as $filter) {
		if (!is_array($filter)) {
			continue;
		}

		$label = isset($filter['name']) && is_string($filter['name']) && $filter['name'] !== ''
			? $filter['name']
			: 'Filtr';
		$slug = sanitize_title($label);
		if ($slug === '') {
			$slug = 'filtr';
		}
		$baseSlug = $slug;
		$suffix = 2;
		while (isset($usedFilterSlugs[$slug])) {
			$slug = $baseSlug . '-' . $suffix;
			$suffix++;
		}
		$usedFilterSlugs[$slug] = true;

		$views[] = [
			'label' => $label,
			'query' => $slug,
			'filters' => $filter,
		];
	}

	$selectedFilter = isset($_GET['jen']) && is_string($_GET['jen'])
		? sanitize_title(wp_unslash($_GET['jen']))
		: '';
	$activeViewIndex = 0;
	if ($selectedFilter !== '') {
		foreach ($views as $index => $view) {
			if ($view['query'] === $selectedFilter) {
				$activeViewIndex = $index;
				break;
			}
		}
	}

	$markup = sprintf(
		'<div class="bis-akce" data-bis-akce-endpoint="%s" data-bis-akce-limit="%d"><div class="bis-akce__tabs" role="group" aria-label="Přepnout výpis akcí">',
		esc_url(rest_url('brontosaurus/v1/events')),
		$limit,
	);
	$panels = '';
	foreach ($views as $index => $view) {
		$panelId = wp_unique_id('bis-akce-view-');
		$isActive = $index === $activeViewIndex;
		$markup .= sprintf(
			'<div class="wp-block-button is-style-%s"><button class="wp-block-button__link wp-element-button bis-akce__tab" type="button" aria-controls="%s" aria-pressed="%s" data-bis-akce-view="%s" data-bis-akce-filter="%s" data-bis-akce-filters="%s">%s</button></div>',
			$isActive ? 'primary' : 'secondary',
			esc_attr($panelId),
			$isActive ? 'true' : 'false',
			esc_attr($panelId),
			esc_attr($view['query']),
			esc_attr(wp_json_encode($view['filters'], JSON_UNESCAPED_UNICODE)),
			esc_html($view['label']),
		);
		$panels .= sprintf(
			'<div class="bis-akce__view" id="%s" data-bis-akce-panel%s>%s</div>',
			esc_attr($panelId),
			$isActive ? '' : ' hidden',
			$isActive ? brontosaurus_bis_events_markup($view['filters'], $limit) : '',
		);
	}

	return $markup . '</div><div class="bis-akce__views">' . $panels . '</div></div>';
}

function brontosaurus_bis_events_markup(array $attributes, int $limit, int $offset = 0): string
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

	$cacheKey = 'brontosaurus_bis_akce_' . md5(implode(':', [
		...$cacheFilters,
		(string) $limit,
		(string) $offset,
	]));
	$cachedMarkup = get_transient($cacheKey);
	if (is_string($cachedMarkup)) {
		return $cachedMarkup;
	}

	$parameters = (new EventParameters())
		->setPeriod(Period::FUTURE_ONLY)
		->setLimit($offset + $limit + 1)
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

	if ($events === []) {
		if ($offset > 0) {
			return '';
		}

		$markup = '<p class="bis-akce__message">Momentálně nejsou vypsané žádné akce.</p>';
		set_transient($cacheKey, $markup, 5 * MINUTE_IN_SECONDS);
		return $markup;
	}

	$hasMore = count($events) > $offset + $limit;
	$events = array_slice($events, $offset, $limit);
	$items = '';
	foreach ($events as $event) {
		$startDate = $event->startDate;
		$endDate = $event->endDate;
		$date = brontosaurus_format_bis_event_date($startDate, $endDate);
		$location = $event->location->name;
		$coverPhotoPath = $event->coverPhotoPath?->mediumSizePath;
		$eventUrl = sprintf('https://bis.brontosaurus.cz/akce/%d/', $event->id);
		$isFull = $event->registration->isEventFull;
		$introduction = wp_html_excerpt(
			wp_strip_all_tags($event->propagation->invitationTextIntroduction),
			200,
			'…',
		);
		$labels = [];
		if ($event->program === Program::NATURE) {
			$labels[] = ['name' => 'akce příroda', 'modifier' => 'nature'];
		}
		if ($event->program === Program::MONUMENTS) {
			$labels[] = ['name' => 'akce památky', 'modifier' => 'sights'];
		}
		if ($event->program === Program::HOLIDAYS_WITH_BRONTOSAURUS) {
			if ($event->category === Category::VOLUNTEERING) {
				$labels[] = ['name' => 'dobrovolnická'];
			} elseif ($event->category === Category::EXPERIENTAL) {
				$labels[] = ['name' => 'zážitková'];
			}
			$labels[] = ['name' => 'prázdninová'];
		} elseif ($event->duration === 1) {
			$labels[] = ['name' => 'jednodenní'];
		} elseif ($event->group === Group::WEEKEND_EVENT) {
			$labels[] = ['name' => 'víkendovka'];
		} elseif ($event->group === Group::OTHER) {
			$labels[] = ['name' => 'dlouhodobá'];
		}

		$items .= '<li class="bis-akce__item">';
		$items .= sprintf(
			'<a class="hb-event%s" href="%s">',
			$isFull ? ' hb-event--full' : '',
			esc_url($eventUrl),
		);
		$items .= '<div class="hb-event__imageWrapper">';
		$items .= sprintf(
			'<img class="hb-event__image%s" src="%s" alt="" loading="lazy">',
			$coverPhotoPath === null ? ' hb-event__image--noThumbnail' : '',
			esc_url($coverPhotoPath ?? 'https://brontosaurus.cz/wp-content/uploads/2024/12/logo-hb-brontosaurus.svg'),
		);
		if ($labels !== []) {
			$items .= '<div class="hb-event__labels"><div class="hb-eventLabels">';
			foreach ($labels as $label) {
				$modifierClass = isset($label['modifier'])
					? ' hb-eventLabels__item--type hb-eventLabels__item--' . esc_attr($label['modifier'])
					: '';
				$items .= sprintf(
					'<div class="hb-eventLabels__item%s">%s</div>',
					$modifierClass,
					esc_html($label['name']),
				);
			}
			$items .= '</div></div>';
		}
		$items .= '</div>';
		if ($isFull) {
			$items .= '<span class="hb-event__full">plně obsazeno</span>';
		}
		$items .= '<header class="hb-event__header">';
		$items .= sprintf(
			'<h3 class="hb-event__heading">%s</h3>',
			esc_html($event->name),
		);
		if ($event->tags !== []) {
			$items .= '<div class="hb-event__tags">';
			foreach ($event->tags as $tag) {
				$items .= sprintf('<span class="hb-event__tag">%s</span>', esc_html($tag->name));
			}
			$items .= '</div>';
		}
		$items .= '<div class="hb-event__meta">';
		$items .= sprintf(
			'<time class="hb-event__date" datetime="%s">%s</time>',
			esc_attr($startDate->format('Y-m-d')),
			esc_html($date),
		);
		if ($location !== '') {
			$items .= sprintf(
				'<span class="hb-event__place" title="Místo konání">%s</span>',
				esc_html($location),
			);
		}
		$items .= '</div></header>';
		if ($introduction !== '') {
			$items .= sprintf('<p class="hb-event__excerpt">%s</p>', esc_html($introduction));
		}
		$items .= '</a></li>';
	}

	$markup = '<ul class="bis-akce__list">' . $items . '</ul>';
	if ($hasMore) {
		$markup .= sprintf(
			'<div class="wp-block-button is-style-secondary bis-akce__more"><button class="wp-block-button__link wp-element-button" type="button" data-bis-akce-more data-offset="%d">Zobrazit další</button></div>',
			$offset + count($events),
		);
	}
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
