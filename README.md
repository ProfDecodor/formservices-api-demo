# FormServices API Demo

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-8892BF.svg?style=flat-square)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-12.x-FF2D20.svg?style=flat-square)](https://laravel.com)
[![Package](https://img.shields.io/badge/uses-profdecodor%2Fformservices--api--client-blue.svg?style=flat-square)](https://github.com/profdecodor/formservices-api-client)

A Laravel 12 demo and testing application for the [`profdecodor/formservices-api-client`](https://github.com/profdecodor/formservices-api-client) package.

This project serves a dual purpose:
- **Demo** — explore what the package can do through a live, browsable UI
- **Test bed** — validate the package against a real FormServices API instance and drive its development

## Features

- Live API calls with results rendered in a clean Bootstrap 5 UI
- Each screen displays the **exact PHP code** used to make the API call (with syntax highlighting)
- Covers all major API resources progressively as they are implemented
- Error handling shown directly in the UI

## Tech Stack

| Layer | Technology |
|-------|------------|
| Framework | Laravel 12, PHP 8.2 |
| CSS | Bootstrap 5.3 + Bootstrap Icons 1.11 (CDN) |
| Syntax highlighting | Highlight.js 11.9 (CDN) |
| API client | `profdecodor/formservices-api-client` |

## Screens

| Screen | Route | API call | Status |
|--------|-------|----------|--------|
| Dashboard | `/` | — | ✅ Done |
| Applications | `/applications` | `applications()->findAll()` | ✅ Done |
| Application detail | `/applications/{id}` | `applications()->find()` + `getMetadata()` | ✅ Done |
| Files | `/files` | `files()->findManagedWithHeaders()` | ✅ Done |
| File detail | `/files/{uuid}` | `files()->find()` (documents embedded) | ✅ Done |
| Auth | `/auth` | `auth()->me()` | ✅ Done |
| File Creation | `/start` | `start()->findAll()` + `start()->start()` | 🔜 Planned |
| Attachments | `/attachments` | `attachments()->findByFile()` | 🔜 Planned |
| Documents | `/documents` | `documents()->findAll()` | 🔜 Planned |
| Contents | `/contents` | `contents()->findAll()` | 🔜 Planned |
| Projects | `/projects` | `projects()->findBuild()` | 🔜 Planned |

## Requirements

- PHP 8.2+
- Laravel 12.x
- MySQL or SQLite (for sessions and cache)
- Access to a FormServices API instance

## Installation

### 1. Install the package

This project requires `profdecodor/formservices-api-client`. Follow the [installation instructions](https://github.com/profdecodor/formservices-api-client#installation) from the package README to add it to your `composer.json`, then update the `repositories` section of this project's `composer.json` accordingly.

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your FormServices API credentials:

```env
FORMSERVICES_MAIN_URL=https://your-formservices-instance.com/portal
FORMSERVICES_MAIN_LOGIN=your-username
FORMSERVICES_MAIN_KEY=your-api-key
FORMSERVICES_MAIN_TIMEOUT=30
FORMSERVICES_MAIN_VERSION=2023
FORMSERVICES_MAIN_VERIFY_SSL=true
```

### 4. Set up the database

```bash
php artisan migrate
```

> SQLite works fine for local development — set `DB_CONNECTION=sqlite` in `.env`.

### 5. Serve the application

**With Laragon / Apache vhost** (recommended):

Point a vhost to the `public/` directory, e.g. `http://formservices-api-demo.test`.

**With the built-in server:**

```bash
php artisan serve
```

## Project Structure

```
app/Http/Controllers/
├── DashboardController.php          # Dashboard with API client status
├── ApplicationController.php        # Applications list + detail
├── FileController.php               # Files list + detail
└── AuthController.php               # Authenticated user info

resources/views/
├── layouts/app.blade.php            # Bootstrap 5 layout (navbar + sidebar)
├── partials/code-snippet.blade.php  # Reusable code display component
├── dashboard.blade.php
├── applications/
│   ├── index.blade.php
│   └── show.blade.php
├── files/
│   ├── index.blade.php
│   └── show.blade.php
└── auth/
    └── index.blade.php
```

## Code Snippet Component

Every screen includes a collapsible panel showing the PHP code used to make the API call:

```blade
@include('partials.code-snippet', [
    'title'     => 'API Call — auth()->me()',
    'code'      => $codeSnippet,
    'collapsed' => true,
])
```

The snippet is built dynamically in the controller and reflects active filters or parameters where applicable.

## Related

- **[profdecodor/formservices-api-client](https://github.com/profdecodor/formservices-api-client)** — the Laravel package this project demos and tests

---

**Developed by Julian Davreux**