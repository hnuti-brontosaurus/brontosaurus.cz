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

Blok **Akce z BIS** zobrazuje nadcházející akce z Brontosauřího informačního systému.
V nastavení bloku lze vybrat štítek a počet zobrazovaných akcí od 1 do 12.
Seznam akcí se vykresluje na serveru a ukládá do mezipaměti na pět minut.

Před aktivací šablony nainstalujte závislosti PHP:

```bash
composer install
```
