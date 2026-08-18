<?php

add_action( 'after_setup_theme', function() {
	add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
} );

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'brontosaurus-style', get_stylesheet_uri() );
} );

add_action( 'enqueue_block_editor_assets', function() {
    wp_enqueue_script(
        'brontosaurus-editor',
        get_theme_file_uri( 'scripts/editor.js' ),
        [ 'wp-blocks', 'wp-hooks', 'wp-data' ],
        filemtime( get_theme_file_path( 'scripts/editor.js' ) ),
        true,
    );
} );
