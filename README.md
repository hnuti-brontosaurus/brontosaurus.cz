# Základ šablony Brontosaurus FSE

Tato složka obsahuje základní šablonu WordPressu pro úpravy celého webu (Full Site Editing).

## Obsah

- `style.css` — metadata šablony a styly.
- `functions.php` — nastavení šablony a podpora bloků.
- `theme.json` — konfigurace šablony pro FSE.
- `index.php` — povinná záložní šablona.
- `templates/index.html` — šablona úvodní stránky.
- `templates/single.html` — šablona příspěvku.
- `parts/header.html` — část šablony pro záhlaví.
- `parts/footer.html` — část šablony pro zápatí.

## Blok akcí z BIS

Blok **BIS akce** zobrazuje nadcházející akce z Brontosauřího informačního systému.
V nastavení bloku lze filtrovat podle programu, cílové skupiny, kategorie, typu akce,
kraje a štítku. Výchozí počet je 9 akcí; lze nastavit 3 až 15.
Na širších obrazovkách se akce zobrazují ve třech sloupcích.
Seznam akcí se vykresluje na serveru a ukládá do mezipaměti na pět minut.

Před aktivací šablony nainstalujte závislosti PHP:

```bash
composer install
```
