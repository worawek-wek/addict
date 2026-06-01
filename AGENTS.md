# Repository Guidelines

## Project Structure & Module Organization

This is a Laravel 9 application. Core PHP code lives in `app/`, with controllers in `app/Http/Controllers`, models in `app/Models`, providers in `app/Providers`, and console commands in `app/Console/Commands`. Routes are defined in `routes/web.php`, `routes/api.php`, `routes/channels.php`, and `routes/console.php`.

Frontend sources live in `resources/`: Blade views in `resources/views`, JavaScript in `resources/js`, Tailwind/PostCSS styles in `resources/css`, and images/fonts in `resources/images` and `resources/fonts`. Public assets are served from `public/`. Migrations, seeders, and factories are in `database/`. Tests are split into `tests/Feature` and `tests/Unit`.

## Build, Test, and Development Commands

- `composer install`: installs PHP dependencies from `composer.lock`.
- `cp .env.example .env` then `php artisan key:generate`: creates local environment configuration.
- `php artisan migrate`: applies database migrations.
- `php artisan serve`: runs the development server; XAMPP Apache can also serve `public/`.
- `npm install`: installs frontend dependencies.
- `npm run dev`: builds frontend assets once with Laravel Mix.
- `npm run watch`: rebuilds assets on changes, including PostCSS output.
- `npm run prod`: builds production assets with minification.
- `php artisan test` or `vendor/bin/phpunit`: runs the PHPUnit test suite.

## Coding Style & Naming Conventions

Follow `.editorconfig`: UTF-8, LF line endings, final newline, trimmed trailing whitespace, and 4-space indentation; YAML uses 2 spaces. PHP follows the Laravel StyleCI preset for PHP 8. Use PSR-4 namespaces from `composer.json` (`App\`, `Tests\`, `Database\Factories`, `Database\Seeders`). Name classes in PascalCase, methods and variables in camelCase, migrations with Laravel timestamped snake_case names, and Blade files with descriptive kebab-case or existing conventions.

## Testing Guidelines

Place HTTP and workflow tests in `tests/Feature`; place isolated class tests in `tests/Unit`. Test files must end with `Test.php` so `phpunit.xml` discovers them. Prefer Laravel testing helpers and factories over manual setup. For database behavior, document required seed data or add factories/seeders.

## Commit & Pull Request Guidelines

Recent history uses short messages such as `update`, dated notes, and brief change summaries. Keep new commits concise but descriptive, for example `Fix branch booking sort order` or `Update POS receipt export`. Pull requests should include a problem summary, key changes, test results, linked issue or task ID when available, and screenshots for UI changes.

## Security & Configuration Tips

Do not commit real secrets from `.env`; update `.env.example` when adding required configuration keys. Keep generated files in `storage/` and compiled assets in `public/` out of reviews unless they are intentionally changed.
