<?php declare(strict_types=1);

namespace HnutiBrontosaurus\Theme;

final class Meta
{
	public static function coverPhoto(): void
	{
		register_post_meta( 'page', 'hb_cover_photo_image_id', [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'integer',
			'sanitize_callback' => 'absint',
			'auth_callback' => '__return_true',
		] );

		register_post_meta( 'page', 'hb_cover_photo_image_external_url', [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback' => '__return_true',
		] );
	
		register_post_meta( 'page', 'hb_cover_photo_position_x', [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => '__return_true',
		] );
	
		register_post_meta( 'page', 'hb_cover_photo_position_y', [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => '__return_true',
		] );
	
		register_post_meta( 'page', 'hb_cover_photo_size', [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => '__return_true',
		] );
	}
}