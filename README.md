# Brontosaurus WordPress Theme

Toto repozitář obsahuje WordPress šablonu pro web Hnutí Brontosaurus.
Projekt je zaměřený na to, aby byl celý web spravovatelný v WordPressu, ale zároveň zachoval pevné grafické a obsahové koncepce designu.

Repozitář není plný WordPress projekt od nuly — jde o samotnou šablonu a její logiku, která se ve vývoji připojuje k běžící WordPress instalaci.

## Co je v repozitáři

- PHP šablony a funkce pro WordPress (`functions.php`, `header.php`, `footer.php`, `template-parts/`, atd.)
- vlastní datové kontejnery a helpery (`DataContainers`, `Filters`, `Meta.php`, `Configuration.php`)
- front-end zdroje v `frontend/src/styles` a build pipeline přes Gulp
- konfigurace pro statickou analýzu typu PHPStan
- setup pro vývoj v devcontaineru / Codespaces (`.devcontainer/`)

## Technologie

- PHP + WordPress
- Composer pro PHP závislosti
- Node.js + npm pro frontend build
- Gulp + Sass pro kompilaci stylů

## Požadavky

Před vývojem je potřeba mít nainstalované:

- PHP (v projektu je nastaven platform constraint pro PHP 8.5)
- Composer
- Node.js a npm

## Rychlý start

```bash
# instalace PHP závislostí
composer install

# instalace front-end závislostí
npm install
```

Pro lokální vývoj je vhodné připojit šablonu jako WordPress theme, například jako symlink v adresáři `wp-content/themes/brontosaurus`.

## Frontend build

```bash
# watch mód – kompilace při změně souborů
npm run dev

# produkční build
npm run build
```

V současnosti Gulp zpracovává zejména SCSS styly do výsledných CSS souborů v `frontend/dist/css`.

## Vývojový setup

V repozitáři je připraven devcontainer a inicializační skript `.devcontainer/init.sh`, který:

- vytvoří WordPress konfiguraci
- nainstaluje WordPress do `/var/www/html`
- vytvoří základní stránky
- nastaví homepage
- nainstaluje závislosti projektu a spustí build
- symlinkne repo jako theme `brontosaurus`

Tento setup je vhodný pro lokální vývoj i pro GitHub Codespaces.

## Statická analýza

Pro PHPStan je definován script:

```bash
composer run phpstan
```

## Struktura důležitých souborů

- `composer.json` – PHP dependencies a scripts
- `package.json` – frontend dependencies a Gulp scripts
- `gulpfile.babel.js` – konfigurace build procesu
- `config/` – konfigurační soubory pro projekt
- `frontend/src/styles/` – zdrojové SCSS soubory
- `.devcontainer/init.sh` – bootstrap development prostředí

## Poznámka

Repozitář je určen pro vývoj webu Hnutí Brontosaurus a není samostatně běžící aplikací bez WordPress instalace.