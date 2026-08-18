<?php
/**
 * Brontosaurus FSE Theme functions and definitions.
 */

add_action( 'after_setup_theme', function() {
    // Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Featured images.
	add_theme_support( 'post-thumbnails' );

	// Block editor support.
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );

	// Optional: load editor.css in the editor.
	add_editor_style( 'editor.css' );

    add_theme_support( 'align-wide' );
    add_theme_support( 'custom-line-height' );
    add_theme_support( 'custom-spacing' );
    add_theme_support( 'custom-units' );
    add_theme_support( 'core-block-patterns' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'menus' );
    add_theme_support( 'block-template-parts' );

    register_nav_menus( [
        'header' => __( 'Hlavní navigace', 'brontosaurus' ),
    ] );
} );

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'brontosaurus-style', get_stylesheet_uri() );
} );
