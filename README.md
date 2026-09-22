# JCORE Ydin (JYDIN)
The core module for JCORE.

Ydin holds the code that is the same in every JCORE project. The theme is copied
into a project and detached from its repository, so anything that may need to be
updated later belongs here rather than in the theme.

## Bootstrap

`Bootstrap::init()` starts the parts every project needs — Timber, the Timber
context, the Customizer and the environment handler — and loads the modules
registered through the `jcore_theme_load_modules` filter.

```php
use Jcore\Ydin;

Ydin\Bootstrap::init();

add_filter(
    'jcore_theme_load_modules',
    function ( $modules ) {
        $modules[] = \Jcore\Oikeus\Bootstrap::class;
        return $modules;
    }
);
```

## Features

Everything else is opt-in: the theme calls `init()` on the features it wants.
Calling `init()` twice is a no-op.

| Feature | What it does |
| --- | --- |
| `Settings\AcfOptions` | ACF options pages built from a filterable array (`jcore_init_settings_fields`). Ships the "Keys & IDs" group. |
| `Settings\Customizer` | Customizer sections and the colour controls. Started by `Bootstrap`. |
| `WordPress\Acf` | Stores ACF field groups, post types and taxonomies as JSON in the theme, under readable file names. |
| `WordPress\Admin` | Loads the theme's admin stylesheet. |
| `WordPress\Analytics` | Google Analytics, Google Tag Manager and Matomo snippets, from the Keys & IDs settings. |
| `WordPress\Blocks` | Registers every built block in the theme's `dist/blocks`, adds the JCORE block category. |
| `WordPress\Comments` | Turns comments off across front end, admin and REST. |
| `WordPress\Editor` | Block editor policy: restricted blocks and variations, spacer styles, core pattern removal. |
| `WordPress\Forms` | Gravity Forms integration (submit button classes, confirmation anchor). |
| `WordPress\Login` | Login screen branding and stylesheet. |
| `WordPress\Media` | SVG uploads, JPEG quality, optional inline SVG logo. |
| `WordPress\Menus` | Registers the menus declared through `jcore_menus`, adds a page-slug body class. |
| `WordPress\ThemeSupport` | The shared `add_theme_support` baseline. |

## Utilities

- `WordPress\Assets` — script and style registration with automatic cache busting.
- `WordPress\PostType` / `WordPress\Taxonomy` — registration wrappers with sane defaults.
- `Timber\ContextProvider` — the shared Timber context, Twig filters and functions.
- `Jcore\Ydin\register_timber_location()` — add a Twig template directory.

## Filters

Each feature documents its own filters in the class. The ones a project reaches
for most often:

| Filter | Purpose |
| --- | --- |
| `jcore_menus` | The navigation menus to register and expose to Timber. |
| `jcore_init_settings_fields` | Add groups and fields to the ACF options pages. |
| `jcore_theme_load_modules` | Modules (plugin bootstraps) to initialize. |
| `jcore_restricted_blocks` | Core blocks to remove from the inserter. |
| `jcore_restricted_block_variations` | Block variations to remove, keyed by block. |
| `jcore_blocks_directories` | Where to look for built blocks. |
| `jcore_editor_stylesheet` | Path to the editor stylesheet, relative to the theme. |
| `jcore_admin_stylesheet` / `jcore_login_stylesheet` | Admin and login stylesheets. |
| `jcore_inline_svg_logo` | Replace an SVG custom logo with the inline SVG. |
| `jcore_upload_mimes` / `jcore_jpeg_quality` | Upload and image handling. |
| `jcore_local_mail_host` / `jcore_local_mail_port` | Where local mail is caught. |
| `jcore_analytics_enabled` / `jcore_analytics_skip_logged_in` | When analytics are printed. |
