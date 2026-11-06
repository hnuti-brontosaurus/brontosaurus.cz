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

Blok **BIS akce** zobrazuje akce z Brontosauřího informačního systému.
V sekci **Výpis akcí** lze nastavit výchozí výpis podle programu, cílové skupiny,
kategorie, typu akce, kraje a štítku. V sekci **Nastavení filtrů** lze vytvářet
pojmenované sady filtrů, mezi kterými návštěvníci přepínají nad výpisem akcí.
V každém filtru lze vybrat více možností; bez výběru se daný filtr nepoužije.
Aktivní filtr se ukládá do URL jako `?jen=<slug-filtru>`; odkaz s parametrem vybere
filtr také při načtení stránky a přepnutí filtru URL aktualizuje bez obnovení stránky.
Pořadí vlastních filtrů lze měnit přetažením nebo tlačítky; filtr „vše“ zůstává první.
Výchozí počet je 9 akcí; lze nastavit 3 až 15.
Na širších obrazovkách se akce zobrazují ve třech sloupcích.
Seznam akcí se vykresluje na serveru a ukládá do mezipaměti na pět minut.

Před aktivací šablony nainstalujte závislosti PHP:

```bash
composer install
```
