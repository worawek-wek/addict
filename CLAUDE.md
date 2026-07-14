# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Laravel 9 / PHP 8 application for managing a spa/massage + apartment-rental business (Thai-language UI). Served from XAMPP (`public/` is the web root, MySQL backend). Frontend is the "Enigma" TailwindCSS admin template built with Laravel Mix. Much of the codebase, view files, and comments are in Thai.

## Commands

```bash
# Setup
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate            # see "Database" caveat below

# Run (XAMPP Apache serves public/, or:)
php artisan serve

# Frontend assets (Laravel Mix + a SEPARATE PostCSS step for app.css)
npm run dev                    # one-off build
npm run watch                  # rebuild on change (runs postcss + mix concurrently)
npm run prod                   # minified production build

# Tests
php artisan test               # or: vendor/bin/phpunit
php artisan test --filter=SomeTest          # single test class/method
vendor/bin/phpunit --filter testMethodName
```

Visit `/clc` in the browser to clear+rebuild all caches (cache, config, view, route) — a convenience route defined in `routes/web.php`.

## Architecture

### Three app areas, two auth guards

Auth is split across two Eloquent guards (`config/auth.php`):

- **`web` guard → `App\Models\User`** — staff/back-office. Powers two areas:
  - **Admin** (`/admin` prefix, `auth` middleware) — the main back office: users/employees, products, drinks, rooms, courses, room-types, orders, commissions, reports, settings. Controllers in `app/Http/Controllers/` (root + `Admin/`).
  - **POS** (`/pos` prefix, `auth` middleware) — point-of-sale for services/products/drinks with a cart flow. Controllers in `app/Http/Controllers/pos/`.
- **`customer` guard → `App\Models\Customer`** — customer-facing online booking at the site root (`/login`, `/register`, `/home`, `/{branch}/service/{id}`). Controllers in `app/Http/Controllers/Front/`.

Route middleware aliases (`app/Http/Kernel.php`): `auth` (staff), `guest:customer` / `auth:customer` (customer guard), and `loggedin` (`LoggedIn.php` — redirects already-authenticated staff away from the login/register pages).

Routes for all areas live in a single large `routes/web.php` (~460 lines) — grouped by prefix. `routes/api.php` is minimal.

### Domain model

61 Eloquent models in `app/Models/`. The core entities:

- **Orders** — `Order` is central and overloaded: it represents massage-service bookings *and* is reused for product/drink sales (`OrderHasProduct`, `OrderHasDrink`, `OrderHasAddonOption`). Admin order views are split into `Admin\OrderRoomController` (services), `OrderProductController`, `OrderDrinkController`.
- **Catalog** — `Course`, `Product`/`ProductType`, `Drink`, `Room`/`RoomType`/`RoomGroupModel`, `AddonOption`, `Service`, `Branch`.
- **Commissions** — massage staff, sales staff, and drink-sales commissions each have their own tiers/history (`SalesCommissionTier`, `MassageCommission`, `CheerCharge`, `CommissionsHistory`, `HistoryCommission`, `UserHasRoomTypeCommission`, `UserHasOptionCommission`). Calculation logic lives in `app/Support/CourseCommissionCalculator.php`.
- **Stock** — card/drink stock lifecycle: `CardStocks`, `DrinkCardStocks`, `StockReadyForSale`, `DrinkStockReadyForSale`, `ExportStock`, `HistoryStock`.
- **Apartment side** — a separate rental sub-domain: `Apartment`, `Building`, `Floor`, `Room`/`RoomForRents`, `Renter`, `RentBill`, `Meter`, `Contract`, plus Thai address models (`Province`/`District`/`Subdistrict`).
- **Sales closures** — `DailySalesClosure` groups orders into settlement rounds (referenced as `ref_daily_sales_closure_id`); product/drink order controllers expose `closures` and history-by-round endpoints.

### The "business day" convention (important)

A business day runs **10:01 → 10:00 the next day**, not midnight-to-midnight. All reporting, date-filtering, and closure logic must go through `app/Support/AdminBusinessDay.php` (`currentRange()`, `rangeFromRequest()`, `singleDateRange()`, `sqlRange()`, etc.) rather than raw date math. `DEFAULT_PER_PAGE` is also defined there.

### Conventions

- **DataTables pattern** — list controllers typically pair an `index()` (renders the Blade page) with a `datatable()` method that returns server-side JSON for the Tabulator/DataTables grid. Follow this split when adding new list screens.
- **PDF/Excel export** — reports export via mPDF (`PDFController`, `ExportExcelController`, per-controller `pdf()` methods) and PhpSpreadsheet.
- **Global view data** — `App\Providers\ViewServiceProvider` registers view composers on `*` (every view): menu, dark-mode, logged-in user, color scheme. Menu structure is defined in `app/Main/{SideMenu,TopMenu,SimpleMenu}.php`.
- **Blade views** — under `resources/views/{admin,frontend,home,pos,clock-in}`. Compiled assets output to `public/dist/`.

### Database caveat

`database/migrations/` contains only ~8 migrations (Laravel defaults + a few recent additions) — it does **not** describe the full schema. Most of the 61 tables were created outside Laravel migrations (imported SQL dump). To understand a table's columns, read the model and existing queries rather than relying on migrations; when adding columns, add a migration *and* verify against the live DB.

## Known deferred work

`COMMISSION_FUTURE_WORK.txt` describes a "Mama team rank" commission system (5-rank, percentage-or-round based). It is explicitly **not to be implemented yet** — saved for later discussion.
