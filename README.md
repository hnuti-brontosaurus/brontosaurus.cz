# Brontosaurus FSE Theme Skeleton

This folder contains a minimal WordPress Full Site Editing theme skeleton.

## Contents

- `style.css` — theme metadata and header.
- `functions.php` — theme setup and block support.
- `theme.json` — FSE theme configuration.
- `index.php` — required fallback file.
- `templates/index.html` — front page template.
- `templates/single.html` — single post template.
- `parts/header.html` — header template part.
- `parts/footer.html` — footer template part.

## BIS events block

The **Akce z BIS** block lists upcoming events from the Brontosaurus information system.
In the block settings, choose an optional tag and display between 1 and 12 events.
The event list is rendered on the server and cached for five minutes.

Install the PHP dependency before activating the theme:

```bash
composer install
```
