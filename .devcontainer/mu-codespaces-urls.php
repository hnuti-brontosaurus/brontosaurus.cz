<?php
/**
 * Plugin Name: Codespaces URL Handler
 * Description: Automatically handles URLs for GitHub Codespaces environment
 * Version: 1.1
 */

if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && str_contains($_SERVER['HTTP_X_FORWARDED_HOST'], 'app.github.dev')) {
    $codespace_url = 'https://' . $_SERVER['HTTP_X_FORWARDED_HOST'];

    define('WP_HOME', $codespace_url);
    define('WP_SITEURL', $codespace_url);

    // Fix SERVER_PORT so WordPress doesn't try to add :443 or :80
    $_SERVER['SERVER_PORT'] = 443;
    $_SERVER['HTTPS'] = 'on';

    // Disable canonical redirects which mangle Codespaces URLs
    remove_filter('template_redirect', 'redirect_canonical');
}