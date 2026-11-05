<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/blocks/bis-akce.php';

add_action( 'after_setup_theme', function() {
	add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'post-thumbnails' );
	add_editor_style( 'style.css' );
} );

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'brontosaurus-style', get_stylesheet_uri() );
} );

add_action( 'enqueue_block_editor_assets', function() {
    wp_enqueue_style(
        'brontosaurus-editor',
        get_theme_file_uri( 'editor.css' ),
        [],
        filemtime( get_theme_file_path( 'editor.css' ) ),
    );

    wp_enqueue_script(
        'brontosaurus-editor',
        get_theme_file_uri( 'scripts/editor.js' ),
        [ 'wp-blocks', 'wp-hooks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-rich-text', 'wp-server-side-render' ],
        filemtime( get_theme_file_path( 'scripts/editor.js' ) ),
        true,
    );
} );
