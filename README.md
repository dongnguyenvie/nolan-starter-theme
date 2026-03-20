# Nolan Starter Theme

WordPress starter theme with Alpine.js, Tailwind CSS, esbuild, and BerlinDB.

## Quick Start

1. Copy this theme to `wp-content/themes/your-theme-name/`
2. Run the search & replace (see checklist below)
3. `composer install --optimize-autoloader`
4. `npm install && npm run build`
5. Activate in WordPress

## Search & Replace Checklist

Find and replace across all files:

| Find | Replace with | Context |
|---|---|---|
| `nolan-starter-theme` | `your-theme-name` | folder name, package.json |
| `starter-theme` | `your-theme` | text domain (PHP strings) |
| `starter_theme_` | `your_theme_` | PHP function prefix |
| `StarterTheme` | `YourTheme` | PHP namespace |
| `STARTER_` | `YOURPREFIX_` | PHP constants |
| `starter/v1` | `yourprefix/v1` | REST API namespace |
| `starterData` | `yourData` | JS localized object |
| `starter-` | `your-` | CSS/JS id prefixes, localStorage key, DB table prefix |
| `Nolan Starter Theme` | `Your Theme Name` | display name in style.css |
| `nolan/starter-theme` | `your/theme-name` | composer.json package name |

## Stack

| Layer | Tool | Details |
|---|---|---|
| CSS | Tailwind CSS 3 | Utility-first, dark mode via `class` strategy |
| JS | Alpine.js 3 | Reactive stores, loaded from CDN |
| Bundler | esbuild | Single IIFE bundle, fast builds |
| PHP | Modular | Service/Controller/Repository pattern with PSR-4 |
| ORM | BerlinDB | Custom DB tables with typed rows and fluent queries |

## Build Commands

| Command | What it does |
|---|---|
| `composer install` | Install PHP deps (BerlinDB) + PSR-4 autoloader |
| `npm run build` | Build CSS + JS |
| `npm run build:css` | Tailwind only |
| `npm run build:js` | esbuild only |
| `npm run watch:css` | Watch CSS changes |
| `npm run watch:js` | Watch JS changes |

## File Structure

```
nolan-starter-theme/
├── assets/                          # Compiled output
│   ├── app.min.js
│   └── tailwind.css
├── inc/
│   ├── bootstrap.php                # Module loader
│   ├── theme-page-setup.php         # Auto-create pages + admin sync UI
│   ├── custom-header.php
│   ├── customizer.php
│   ├── template-functions.php
│   ├── template-tags.php
│   ├── Shared/
│   │   └── BaseController.php       # Abstract REST controller
│   └── Module/
│       └── ActionLog/               # Audit trail (core module)
│           ├── ActionLogModule.php   # Boots table + controller + admin
│           ├── ActionLogService.php  # log(), get_for_user(), get_all()
│           ├── ActionLogController.php  # REST: /logs, /logs/me
│           ├── ActionLogAdminPage.php   # WP Admin log viewer
│           └── Db/
│               ├── ActionLogTable.php   # DDL schema
│               ├── ActionLogSchema.php  # Column definitions
│               ├── ActionLogQuery.php   # BerlinDB query
│               └── ActionLogRow.php     # Typed row object
├── js/
│   ├── main.js                      # Entry point
│   ├── theme-api.js                 # WP REST helpers
│   ├── navigation.js                # Mobile menu toggle
│   └── smooth-nav-pjax.js           # Vanilla PJAX navigation
├── src/input.css                    # Tailwind entry
├── template-parts/                  # content, content-page, content-search, content-none
├── functions.php                    # Theme setup, enqueues, autoloader
├── header.php / footer.php          # Navbar + dark mode toggle, footer grid
├── index.php / page.php / single.php / archive.php / search.php / 404.php
├── sidebar.php / comments.php
├── style.css                        # WP theme header + required classes
├── composer.json                    # PHP deps (BerlinDB) + PSR-4 autoload
├── package.json                     # JS/CSS build scripts
└── tailwind.config.js
```

## Page Auto-Setup

The theme can auto-create required pages with correct templates on activation.

1. Create your page template (e.g. `page-about.php`)
2. Register it in `inc/theme-page-setup.php` → `starter_theme_required_pages()`:
   ```php
   [ 'slug' => 'about', 'title' => 'About', 'template' => 'page-about.php' ],
   ```
3. Pages are created on theme activation, or manually via **Appearance → Sync Pages**
4. Idempotent — safe to run multiple times (creates missing, fixes wrong templates)

## Core Module: ActionLog

Audit trail that records user actions with IP tracking. Every project needs this.

**Service usage (from any module):**
```php
use StarterTheme\Module\ActionLog\ActionLogService;

// Log an action
ActionLogService::log( get_current_user_id(), 'order_created', 'order', $id, 'User placed order' );

// System/cron action (user_id = 0)
ActionLogService::log( 0, 'cron_cleanup', '', 0, 'Expired records purged', ['count' => 5] );
```

**REST API:**
| Method | Endpoint | Auth | Description |
|---|---|---|---|
| GET | `/starter/v1/logs` | Admin | All logs (filterable: `action`, `user_id`, `object_type`) |
| GET | `/starter/v1/logs/me` | Login | Current user's logs |
| POST | `/starter/v1/logs` | Login | Create log entry |

**Admin page:** WP Admin → Action Logs — filterable table with user, action, object, message, IP, date.

**DB columns:** `id`, `user_id`, `action`, `object_type`, `object_id`, `message`, `context` (JSON), `ip_address`, `created_at`

## Adding a New Module

1. Create `inc/Module/YourModule/`
2. Add BerlinDB layer: `Db/YourTable.php`, `Db/YourSchema.php`, `Db/YourQuery.php`, `Db/YourRow.php`
3. Add `YourModuleService.php` — business logic
4. Add `YourModuleController.php` extending `BaseController` — REST endpoints
5. Add `YourModuleModule.php` with static `init()` — boots table + controller
6. Register in `inc/bootstrap.php`

## Adding an Alpine Store

1. Create `js/alpine-your-store.js`
2. Import from `theme-api.js` for REST calls
3. Export to `window.yourStore = yourStore`
4. Add `import './alpine-your-store.js'` in `js/main.js`
5. Use `x-data="yourStore()"` in PHP templates
6. Run `npm run build:js`

## Customization

- **Brand colors:** `tailwind.config.js` → `theme.extend.colors.brand`
- **Dark mode:** Class-based toggle, persisted in localStorage
- **Currency:** `formatCurrency()` in `js/theme-api.js`
- **Nav menus:** WP Admin → Appearance → Menus (primary + footer locations)
- **Logo:** WP Admin → Appearance → Customize → Site Identity

## Features

- Dark/light mode with localStorage persistence + flash prevention
- Vanilla PJAX (smooth page transitions, Alpine re-init, progress bar)
- Sticky navbar with `wp_nav_menu` integration
- WP REST API base controller with auth helpers (nonce, login, admin)
- ActionLog module with admin viewer, REST API, IP tracking
- BerlinDB ORM for custom database tables
- Composer PSR-4 autoloading
- esbuild JS bundling (IIFE) + Tailwind CSS purging
- Lazy loading + async decoding on all images
- Accessible skip-to-content link
