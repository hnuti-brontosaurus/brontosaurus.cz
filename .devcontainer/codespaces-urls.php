<?php
/**
 * Obsluha URL pro Codespaces
 *
 * Tento soubor se vkládá na úplný začátek wp-config.php, aby se spustil
 * PŘED načtením nastavení databáze nebo přesměrováním ve WordPressu.
 * Proto jej nelze použít jako mu-plugin — ty se načítají příliš pozdě.
 */

$is_codespaces = boolval(getenv('CODESPACES'));
$codespace_name = getenv('CODESPACE_NAME');
$codespace_domain = getenv('GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN');

if ($is_codespaces && $codespace_name && $codespace_domain) {
    $site_domain = $codespace_name . '-8080.' . $codespace_domain;

    // Přepsat konstanty URL domovské stránky a webu ve WordPressu.
    define('WP_HOME', 'https://' . $site_domain);
    define('WP_SITEURL', 'https://' . $site_domain);

    // Oznámit WordPressu, že je za reverzní proxy HTTPS.
    $_SERVER['HTTPS'] = 'on';

    // Tohle je klíčové: WordPress používá HTTP_HOST pro generování VŠECH
    // URL — trvalých odkazů, adres souborů i přesměrování administrace.
    // Bez toho si stále myslí, že běží na localhost:80, a generuje
    // nesprávné URL i při nastavení výše uvedených konstant.
    $_SERVER['HTTP_HOST'] = $site_domain;

    // Zabránit WordPressu v domněnce, že má přesměrovat na port 80/443.
    $_SERVER['SERVER_PORT'] = '443';
}