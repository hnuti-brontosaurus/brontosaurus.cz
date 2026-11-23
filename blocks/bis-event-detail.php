<?php

use HnutiBrontosaurus\BisClient\BisClientFactory;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\Event\Response\Event;
use HnutiBrontosaurus\BisClient\EventNotFound;

function brontosaurus_bis_event_detail_fields(): array
{
	return [
		'bis-event-name' => 'Název akce',
		'bis-event-cover' => 'Fotka akce',
		'bis-event-date' => 'Datum',
		'bis-event-location' => 'Místo',
		'bis-event-age' => 'Věk',
		'bis-event-start-time' => 'Začátek akce',
		'bis-event-cost' => 'Cena',
		'bis-event-date-location' => 'Datum a místo',
		'bis-event-introduction' => 'Úvod akce',
		'bis-event-practical' => 'Praktické informace',
		'bis-event-work' => 'Popis práce',
		'bis-event-about' => 'O nás',
		'bis-event-details' => 'Další údaje',
		'bis-event-organizers' => 'Organizátor',
		'bis-event-contact' => 'Kontaktní osoba',
		'bis-event-registration' => 'Registrace',
	];
}

function brontosaurus_bis_event_detail_field_name(string $blockName): ?string
{
	$fieldNames = [
		'brontosaurus/bis-event-name' => 'name',
		'brontosaurus/bis-event-cover' => 'cover',
		'brontosaurus/bis-event-date' => 'date',
		'brontosaurus/bis-event-location' => 'location',
		'brontosaurus/bis-event-age' => 'age',
		'brontosaurus/bis-event-start-time' => 'start-time',
		'brontosaurus/bis-event-cost' => 'cost',
		'brontosaurus/bis-event-date-location' => 'date-location',
		'brontosaurus/bis-event-introduction' => 'introduction',
		'brontosaurus/bis-event-practical' => 'practical',
		'brontosaurus/bis-event-work' => 'work',
		'brontosaurus/bis-event-about' => 'about',
		'brontosaurus/bis-event-details' => 'details',
		'brontosaurus/bis-event-organizers' => 'organizers',
		'brontosaurus/bis-event-contact' => 'contact',
		'brontosaurus/bis-event-registration' => 'registration',
	];

	return $fieldNames[$blockName] ?? null;
}

function brontosaurus_register_bis_event_detail_blocks(): void
{
	register_block_type('brontosaurus/bis-event-detail', [
		'api_version' => 3,
		'render_callback' => 'brontosaurus_render_bis_event_detail',
		'provides_context' => [
			'brontosaurus/bis-event-preview-id' => 'previewId',
		],
		'attributes' => [
			'previewId' => [
				'type' => 'string',
				'default' => '',
			],
		],
		'supports' => [
			'html' => false,
		],
	]);

	register_block_type('brontosaurus/bis-event-panel', [
		'api_version' => 3,
		'render_callback' => 'brontosaurus_render_bis_event_panel',
		'provides_context' => [
			'brontosaurus/bis-event-panel' => 'isPanel',
		],
		'attributes' => [
			'isPanel' => [
				'type' => 'boolean',
				'default' => true,
			],
		],
		'supports' => [
			'html' => false,
		],
	]);

	foreach (brontosaurus_bis_event_detail_fields() as $name => $title) {
		register_block_type('brontosaurus/' . $name, [
			'api_version' => 3,
			'render_callback' => 'brontosaurus_render_bis_event_field',
			'uses_context' => [
				'brontosaurus/bis-event-preview-id',
				'brontosaurus/bis-event-panel',
			],
			'attributes' => [
				'field' => [
					'type' => 'string',
					'default' => $name,
				],
				'previewId' => [
					'type' => 'string',
					'default' => '',
				],
				'isPanel' => [
					'type' => 'boolean',
					'default' => false,
				],
			],
			'supports' => [
				'html' => false,
			],
		]);
	}
}
add_action('init', 'brontosaurus_register_bis_event_detail_blocks');

function brontosaurus_render_bis_event_panel(array $attributes, string $content): string
{
	return trim($content) !== '' ? '<div class="bis-event-detail__panel">' . $content . '</div>' : '';
}

function brontosaurus_bis_event_detail_rewrite(): void
{
	add_rewrite_rule(
		'^akce/([0-9]+)/?$',
		'index.php?pagename=akce-detail&bis_event_id=$matches[1]',
		'top',
	);
	add_rewrite_tag('%bis_event_id%', '([0-9]+)');
}
add_action('init', 'brontosaurus_bis_event_detail_rewrite');

function brontosaurus_register_bis_event_detail_query_var(array $vars): array
{
	$vars[] = 'bis_event_id';
	return $vars;
}
add_filter('query_vars', 'brontosaurus_register_bis_event_detail_query_var');

function brontosaurus_get_bis_event_detail_page_id(): int
{
	$page = get_page_by_path('akce-detail');
	if ($page instanceof WP_Post) {
		return $page->ID;
	}

	$template = '<!-- wp:brontosaurus/bis-event-detail -->
<div class="wp-block-brontosaurus-bis-event-detail bis-event-detail"><!-- wp:brontosaurus/bis-event-name /-->
<!-- wp:brontosaurus/bis-event-cover /-->
<!-- wp:brontosaurus/bis-event-panel -->
<!-- wp:brontosaurus/bis-event-date /-->
<!-- wp:brontosaurus/bis-event-location /-->
<!-- wp:brontosaurus/bis-event-age /-->
<!-- wp:brontosaurus/bis-event-start-time /-->
<!-- wp:brontosaurus/bis-event-cost /-->
<!-- wp:brontosaurus/bis-event-contact /-->
<!-- /wp:brontosaurus/bis-event-panel -->
<!-- wp:brontosaurus/bis-event-introduction /-->
<!-- wp:brontosaurus/bis-event-practical /-->
<!-- wp:brontosaurus/bis-event-work /-->
<!-- wp:brontosaurus/bis-event-about /-->
<!-- wp:brontosaurus/bis-event-organizers /--></div>
<!-- /wp:brontosaurus/bis-event-detail -->';

	$pageId = wp_insert_post([
		'post_title' => 'Detail akce',
		'post_name' => 'akce-detail',
		'post_status' => 'publish',
		'post_type' => 'page',
		'post_content' => $template,
	]);

	return is_int($pageId) ? $pageId : 0;
}

function brontosaurus_ensure_bis_event_detail_page(): void
{
	brontosaurus_get_bis_event_detail_page_id();
}
add_action('after_switch_theme', 'brontosaurus_ensure_bis_event_detail_page');
add_action('init', 'brontosaurus_ensure_bis_event_detail_page');

function brontosaurus_flush_bis_event_detail_rewrite_rules(): void
{
	update_option('brontosaurus_bis_event_detail_rewrite_version', '');
}
add_action('after_switch_theme', 'brontosaurus_flush_bis_event_detail_rewrite_rules');

function brontosaurus_maybe_flush_bis_event_detail_rewrite_rules(): void
{
	$version = '1';
	if (get_option('brontosaurus_bis_event_detail_rewrite_version') === $version) {
		return;
	}

	flush_rewrite_rules();
	update_option('brontosaurus_bis_event_detail_rewrite_version', $version);
}
add_action('init', 'brontosaurus_maybe_flush_bis_event_detail_rewrite_rules', 99);

function brontosaurus_bis_event_detail_id_from_value(mixed $value): int
{
	if (is_int($value)) {
		return $value > 0 ? $value : 0;
	}
	if (!is_string($value) || !ctype_digit($value)) {
		return 0;
	}

	$id = (int) $value;
	return $id > 0 ? $id : 0;
}

function brontosaurus_bis_event_detail_id(): int
{
	return brontosaurus_bis_event_detail_id_from_value(get_query_var('bis_event_id'));
}

/**
 * @return array{event: ?Event, error: ?string}
 */
function brontosaurus_load_bis_event_detail(?int $requestedId = null): array
{
	static $loaded = [];

	$id = $requestedId ?? brontosaurus_bis_event_detail_id();
	if ($id <= 0) {
		return ['event' => null, 'error' => 'missing'];
	}
	if (isset($loaded[$id])) {
		return $loaded[$id];
	}

	$cacheKey = 'brontosaurus_bis_event_detail_' . $id;
	$cachedEvent = get_transient($cacheKey);
	if ($cachedEvent instanceof Event) {
		$loaded[$id] = ['event' => $cachedEvent, 'error' => null];
		return $loaded[$id];
	}

	try {
		$event = (new BisClientFactory('https://bis.brontosaurus.cz/api'))
			->create()
			->getEvent($id);
		set_transient($cacheKey, $event, 10 * MINUTE_IN_SECONDS);
		$loaded[$id] = ['event' => $event, 'error' => null];
	} catch (EventNotFound) {
		$loaded[$id] = ['event' => null, 'error' => 'not_found'];
	} catch (ConnectionToBisFailed $exception) {
		error_log('Unable to load BIS event detail #' . $id . ': ' . $exception->getMessage());
		$loaded[$id] = ['event' => null, 'error' => 'connection'];
	}

	return $loaded[$id];
}

function brontosaurus_render_bis_event_detail(array $attributes, string $content, WP_Block $block): string
{
	$id = brontosaurus_bis_event_detail_id();
	if ($id <= 0) {
		return '';
	}

	$loaded = brontosaurus_load_bis_event_detail($id);
	if ($loaded['event'] === null) {
		if ($loaded['error'] === 'not_found') {
			global $wp_query;
			$wp_query->set_404();
			status_header(404);
			return '<p class="bis-event-detail__message" role="alert">Akce nebyla nalezena.</p>';
		}
		return '<p class="bis-event-detail__message" role="alert">Omlouváme se, detail akce se teď nepodařilo načíst. Zkuste to prosím později.</p>';
	}

	return '<div class="bis-event-detail">' . $content . '</div>';
}

function brontosaurus_render_bis_event_field(array $attributes, string $content, WP_Block $block): string
{
	$field = brontosaurus_bis_event_detail_field_name($block->name);
	if ($field === null) {
		return '';
	}

	$isEditorPreview = defined('REST_REQUEST') && REST_REQUEST;
	$id = $isEditorPreview
		? brontosaurus_bis_event_detail_id_from_value($attributes['previewId'] ?? null)
		: brontosaurus_bis_event_detail_id();
	if ($id <= 0) {
		if ($isEditorPreview) {
			return '<p class="bis-event-detail__message">Pro náhled zadejte ID akce v nastavení bloku.</p>';
		}
		return '';
	}
	$loaded = brontosaurus_load_bis_event_detail($id);
	if ($loaded['event'] === null) {
		if ($isEditorPreview) {
			return '<p class="bis-event-detail__message" role="alert">Detail akce se nepodařilo načíst.</p>';
		}
		return '';
	}

	$isPanel = !empty($attributes['isPanel'])
		|| !empty($block->context['brontosaurus/bis-event-panel']);
	return brontosaurus_render_bis_event_field_value($field, $loaded['event'], $isPanel);
}

function brontosaurus_render_bis_event_field_value(string $field, Event $event, bool $isPanel = false): string
{
	$propagation = $event->propagation;

	switch ($field) {
		case 'name':
			return sprintf('<h1 class="bis-event-detail__name">%s</h1>', esc_html($event->name));

		case 'cover':
			$image = $event->coverPhotoPath;
			if ($image === null) {
				return '';
			}
			return sprintf(
				'<img class="bis-event-detail__cover" src="%s" alt="" loading="eager">',
				esc_url($image->largeSizePath),
			);

		case 'date':
			return brontosaurus_render_bis_event_detail_panel_rows([[
				'Datum',
				brontosaurus_format_bis_event_date($event->startDate, $event->endDate),
			]]);

		case 'location':
			return $event->location->name !== ''
				? brontosaurus_render_bis_event_detail_panel_rows([['Místo', $event->location->name]])
				: '';

		case 'age':
			$age = brontosaurus_bis_event_age_range($propagation->minimumAge, $propagation->maximumAge);
			return $age !== ''
				? brontosaurus_render_bis_event_detail_panel_rows([['Věk', $age]])
				: '';

		case 'start-time':
			return $event->startTime !== null
				? brontosaurus_render_bis_event_detail_panel_rows([['Začátek akce', $event->startTime]])
				: '';

		case 'cost':
			return brontosaurus_render_bis_event_detail_panel_rows([
				['Cena', $propagation->cost !== '' ? $propagation->cost : 'bez poplatku'],
			]);

		case 'date-location':
			$date = brontosaurus_format_bis_event_date($event->startDate, $event->endDate);
			if ($isPanel) {
				$items = [['Datum', $date]];
				if ($event->location->name !== '') {
					$items[] = ['Místo', $event->location->name];
				}
				return brontosaurus_render_bis_event_detail_panel_rows($items);
			}

			$markup = '<dl class="bis-event-detail__date-location">';
			$markup .= '<div><dt>Termín</dt><dd>' . esc_html($date);
			if ($event->startTime !== null) {
				$markup .= ' od ' . esc_html($event->startTime);
			}
			$markup .= '</dd></div>';
			if ($event->location->name !== '') {
				$markup .= '<div><dt>Místo</dt><dd>' . esc_html($event->location->name) . '</dd></div>';
			}
			return $markup . '</dl>';

		case 'introduction':
			return brontosaurus_bis_event_text_section(
				'Úvod',
				$propagation->invitationTextIntroduction,
				'bis-event-detail__introduction',
			);

		case 'practical':
			return brontosaurus_bis_event_text_section(
				'Praktické informace',
				$propagation->invitationTextPracticalInformation,
				'bis-event-detail__practical',
			);

		case 'work':
			return brontosaurus_bis_event_text_section(
				'Popis práce',
				$propagation->invitationTextWorkDescription,
				'bis-event-detail__work',
			);

		case 'about':
			return brontosaurus_bis_event_text_section(
				'O nás',
				$propagation->invitationTextAboutUs,
				'bis-event-detail__about',
			);

		case 'details':
			if ($isPanel) {
				$items = [];
				if ($propagation->minimumAge !== null || $propagation->maximumAge !== null) {
					$items[] = ['Věk', brontosaurus_bis_event_age_range($propagation->minimumAge, $propagation->maximumAge)];
				}
				if ($event->startTime !== null) {
					$items[] = ['Začátek akce', $event->startTime];
				}
				$items[] = ['Cena', $propagation->cost !== '' ? $propagation->cost : 'bez poplatku'];
				return brontosaurus_render_bis_event_detail_panel_rows($items);
			}

			$items = [];
			if ($propagation->cost !== '') {
				$items[] = ['Poplatek', $propagation->cost];
			}
			if ($propagation->accommodation !== null) {
				$items[] = ['Ubytování', $propagation->accommodation];
			}
			if ($propagation->minimumAge !== null || $propagation->maximumAge !== null) {
				$items[] = ['Věk', brontosaurus_bis_event_age_range($propagation->minimumAge, $propagation->maximumAge)];
			}
			if ($propagation->workingDays !== null) {
				$items[] = ['Pracovní dny', (string) $propagation->workingDays];
			}
			if ($propagation->workingHours !== null) {
				$items[] = ['Pracovní hodiny', (string) $propagation->workingHours];
			}
			if ($items === []) {
				return '';
			}
			$markup = '<section class="bis-event-detail__details"><h2>Další údaje</h2><dl>';
			foreach ($items as [$label, $value]) {
				$markup .= '<div><dt>' . esc_html($label) . '</dt><dd>' . wp_kses_post($value) . '</dd></div>';
			}
			$markup .= '</dl></section>';
			return $markup;

		case 'organizers':
			if ($propagation->organizers === null) {
				return '';
			}
			return brontosaurus_bis_event_text_section(
				'Organizátor',
				$propagation->organizers,
				'bis-event-detail__organizers',
			);

		case 'contact':
			$contact = $propagation->contactPerson;
			if ($isPanel) {
				$values = [];
				if ($contact->name !== null) {
					$values[] = esc_html($contact->name);
				}
				if ($contact->phoneNumber !== null) {
					$phone = esc_html($contact->phoneNumber);
					$phoneLink = esc_attr(preg_replace('/[^+\d]/', '', $contact->phoneNumber));
					$values[] = '<a href="tel:' . $phoneLink . '">' . $phone . '</a>';
				}
				if ($contact->emailAddress !== '') {
					$email = esc_html($contact->emailAddress);
					$values[] = '<a href="mailto:' . esc_attr($contact->emailAddress) . '">' . $email . '</a>';
				}
				return $values !== []
					? brontosaurus_render_bis_event_detail_panel_rows([['Kontaktní osoba', implode('<br>', $values)]])
					: '';
			}

			$items = [];
			if ($contact->name !== null) {
				$items[] = ['Kontakt', $contact->name];
			}
			if ($contact->emailAddress !== '') {
				$items[] = ['E-mail', '<a href="mailto:' . esc_attr($contact->emailAddress) . '">' . esc_html($contact->emailAddress) . '</a>'];
			}
			if ($contact->phoneNumber !== null) {
				$items[] = ['Telefon', '<a href="tel:' . esc_attr(preg_replace('/[^+\d]/', '', $contact->phoneNumber)) . '">' . esc_html($contact->phoneNumber) . '</a>'];
			}
			if ($items === []) {
				return '';
			}
			$markup = '<section class="bis-event-detail__contact"><h2>Kontaktní osoba</h2><dl>';
			foreach ($items as [$label, $value]) {
				$markup .= '<div><dt>' . esc_html($label) . '</dt><dd>' . $value . '</dd></div>';
			}
			return $markup . '</dl></section>';

		case 'registration':
			$url = $propagation->webUrl ?? sprintf('https://bis.brontosaurus.cz/akce/%d/', $event->id);
			if ($event->registration->isEventFull) {
				return '<p class="bis-event-detail__full">Akce je plně obsazená.</p>';
			}
			if (!$event->registration->isRegistrationRequired) {
				return '';
			}
			return sprintf(
				'<div class="wp-block-button is-style-primary bis-event-detail__registration"><a class="wp-block-button__link wp-element-button" href="%s">Přihlásit se</a></div>',
				esc_url($url),
			);

		default:
			return '';
	}
}

/**
 * @param array<array{0: string, 1: string}> $items
 */
function brontosaurus_render_bis_event_detail_panel_rows(array $items): string
{
	if ($items === []) {
		return '';
	}

	$markup = '<dl class="bis-event-detail__panel-data">';
	foreach ($items as [$label, $value]) {
		$markup .= '<div><dt>' . esc_html($label) . '</dt><dd>' . wp_kses_post($value) . '</dd></div>';
	}
	return $markup . '</dl>';
}

function brontosaurus_bis_event_text_section(?string $heading, ?string $text, string $className): string
{
	if ($text === null || trim(wp_strip_all_tags($text)) === '') {
		return '';
	}
	$headingMarkup = $heading !== null ? '<h2>' . esc_html($heading) . '</h2>' : '';
	return sprintf(
		'<section class="%s">%s<div class="%s__text">%s</div></section>',
		esc_attr($className),
		$headingMarkup,
		esc_attr($className),
		wp_kses_post($text),
	);
}

function brontosaurus_bis_event_age_range(?int $minimum, ?int $maximum): string
{
	if ($minimum !== null && $maximum !== null) {
		return $minimum . '–' . $maximum . ' let';
	}
	if ($minimum !== null) {
		return 'od ' . $minimum . ' let';
	}
	if ($maximum !== null) {
		return 'do ' . $maximum . ' let';
	}
	return '';
}

function brontosaurus_set_bis_event_detail_title(array $title): array
{
	if (brontosaurus_bis_event_detail_id() <= 0) {
		return $title;
	}
	$loaded = brontosaurus_load_bis_event_detail();
	if ($loaded['event'] !== null) {
		$title['title'] = $loaded['event']->name;
	}
	return $title;
}
add_filter('document_title_parts', 'brontosaurus_set_bis_event_detail_title');

function brontosaurus_bis_event_detail_canonical(string $canonical): string
{
	$id = brontosaurus_bis_event_detail_id();
	if ($id <= 0) {
		return $canonical;
	}
	return home_url('/akce/' . $id . '/');
}
add_filter('get_canonical_url', 'brontosaurus_bis_event_detail_canonical');

function brontosaurus_prevent_bis_event_detail_redirect(string|false $redirectUrl, string $requestedUrl): string|false
{
	return brontosaurus_bis_event_detail_id() > 0 ? false : $redirectUrl;
}
add_filter('redirect_canonical', 'brontosaurus_prevent_bis_event_detail_redirect', 10, 2);
