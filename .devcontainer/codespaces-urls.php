<?php
/**
 * Codespaces URL Handler
 *
 * This file is require'd at the very top of wp-config.php so it runs
 * BEFORE WordPress reads any DB options or does any redirects.
 * That's why this can't be a mu-plugin — mu-plugins load too late.
 */

$is_codespaces = boolval(getenv('CODESPACES'));
$codespace_name = getenv('CODESPACE_NAME');
$codespace_domain = getenv('GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN');

if ($is_codespaces && $codespace_name && $codespace_domain) {
    $site_domain = $codespace_name . '-8080.' . $codespace_domain;

    // Override the WordPress home and site URL constants
    define('WP_HOME', 'https://' . $site_domain);
    define('WP_SITEURL', 'https://' . $site_domain);

    // Tell WordPress we're behind an HTTPS reverse proxy
    $_SERVER['HTTPS'] = 'on';

    // This is the critical piece: WordPress uses HTTP_HOST to generate
    // ALL URLs — permalinks, asset URLs, admin redirects, everything.
    // Without this, WP still thinks it's on localhost:80 and generates
    // wrong URLs even with the constants set above.
    $_SERVER['HTTP_HOST'] = $site_domain;

    // Prevent WordPress from thinking it needs to redirect to port 80/443
    $_SERVER['SERVER_PORT'] = '443';
}