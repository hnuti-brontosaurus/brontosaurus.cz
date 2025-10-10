<?php
/**
 * Plugin Name: Codespaces URL Handler
 * Description: Automatically handles URLs for GitHub Codespaces environment
 * Version: 1.0
 * Author: Auto-generated
 */

if (isset($_SERVER['HTTP_HOST']) && !str_contains($_SERVER['HTTP_HOST'], 'app.github.dev')) {
    $codespace_url = $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://' . $_SERVER['HTTP_X_FORWARDED_HOST'];
    define('WP_HOME', $codespace_url);
    define('WP_SITEURL', $codespace_url);
}
