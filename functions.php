<?php
/**
 * Brontosaurus FSE Theme functions and definitions.
 */

add_action( 'after_setup_theme', function() {
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'custom-line-height' );
    add_theme_support( 'custom-spacing' );
    add_theme_support( 'custom-units' );
    add_theme_support( 'core-block-patterns' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
} );
