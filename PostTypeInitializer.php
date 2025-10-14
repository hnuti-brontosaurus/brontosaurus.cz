<?php

namespace HnutiBrontosaurus\Theme;

use WP_Post;
use function add_action;
use function add_meta_box;
use function array_key_exists;
use function esc_attr;
use function get_post_meta;
use function register_post_type;
use function update_post_meta;


final class PostTypeInitializer
{
	public static function novinky(): void
	{
		register_post_type('novinky', [
			'labels' => [
				'name' => 'Novinky',
				'singular_name' => 'Novinka',
				'add_new' => 'Přidat novinku',
				'add_new_item' => 'Přidat novinku',
			],
			'menu_icon' => 'dashicons-megaphone',
			'has_archive' => true,
			'public' => true,
			'show_ui' => true,
			'show_in_rest' => true,
			'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
			'hierarchical' => false,
		]);
	}

	public static function pribehyNadseni(): void
	{
		register_post_type('pribehy-nadseni', [
			'labels' => [
				'name' => 'Příběhy nadšení',
				'singular_name' => 'Příběh nadšení',
				'add_new' => 'Přidat příběh',
				'add_new_item' => 'Přidat příběh',
			],
			'menu_icon' => 'dashicons-testimonial',
			'has_archive' => true,
			'public' => true,
			'show_ui' => true,
			'show_in_rest' => true,
			'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
			'hierarchical' => false,
			'taxonomies' => ['category'],
		]);
		register_taxonomy('kategorie-pribehu', 'pribehy-nadseni', [
			'labels' => [
				'name' => 'Kategorie',
				'singular_name' => 'Kategorie',
				'all_items' => 'Všechny kategorie',
			],
			'hierarchical' => true,
			'public' => true,
			'show_ui' => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
		]);
		add_action('add_meta_boxes', function () {
			add_meta_box(
				'pribehy-nadseni_location',
				'Místo',
				function (WP_Post $post) {
					?>
					<div style="margin-block-end: 1em;">
						<input type="text" name="pribehy-nadseni_location" required><?php echo esc_attr(get_post_meta($post->ID, 'pribehy-nadseni_location', true)); ?></input>
					</div>
					<?php
				},
				'pribehy-nadseni',
				'side',
			);
		});
		add_action('save_post', function ($postId) {
			foreach (['pribehy-nadseni_location'] as $field) {
				if (array_key_exists($field, $_POST)) {
					update_post_meta(
						$postId,
						$field,
						$_POST[$field],
					);
				}
			}
		});
	}

	public static function kontakty(): void
	{
		register_post_type('contacts', [
			'labels' => [
				'name' => 'Kontakty',
				'singular_name' => 'Kontakt',
				'add_new' => 'Přidat kontakt',
				'add_new_item' => 'Přidat kontakt',
			],
			'menu_icon' => 'dashicons-phone',
			'has_archive' => true,
			'public' => true,
			'show_ui' => true,
			'show_in_rest' => false, // disables gutenberg (it is not ready for various styles)
			'supports' => ['title', 'thumbnail'],
			'hierarchical' => false,
		]);
		add_action('add_meta_boxes', function () {
			add_meta_box(
				'contacts_role',
				'Kontakt',
				function (WP_Post $post) {
					?>
					<div style="margin-block-end: 1em;">
						<label for="contacts_role" class="required">Funkce</label><br>
						<textarea name="contacts_role" cols=60" rows="3" id="contacts_role"><?php echo esc_attr(get_post_meta($post->ID, 'contacts_role', true)); ?></textarea>
					</div>

					<div style="margin-block-end: 1em;">
						<label for="contacts_about" class="required">Povídání</label><br>
						<textarea name="contacts_about" cols="60" rows="5" id="contacts_about"><?php echo esc_attr(get_post_meta($post->ID, 'contacts_about', true)); ?></textarea>
					</div>

					<div style="margin-block-end: 1em;">
						<label for="contacts_email" class="required">E-mail</label><br>
						<textarea name="contacts_email" cols=60" rows="3" id="contacts_email"><?php echo esc_attr(get_post_meta($post->ID, 'contacts_email', true)); ?></textarea>
						<br>
						(jeden nebo více adres oddělených novým řádkem)
					</div>
					<?php
				},
				'contacts',
			);
		});
		add_action('save_post', function ($postId) {
			foreach (['contacts_role', 'contacts_about', 'contacts_email'] as $field) {
				if (array_key_exists($field, $_POST)) {
					update_post_meta(
						$postId,
						$field,
						$_POST[$field],
					);
				}
			}
		});
	}
}
