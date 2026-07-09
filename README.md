# DaLin

<div align="center">

*PHP - MySQL • Laravel • Filament • Tailwind*

![PHP 8.5](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel 13.x](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![FilamentPHP v5.x](https://img.shields.io/badge/FilamentPHP-v5.x-FB70A9?style=for-the-badge&logo=filament)
![Tailwind CSS 4.0](https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

</div>

**DaLin** is an information system for running an orienteering club — from race
entries and member finances to shared transport to races. Instead of juggling
spreadsheets, e-mails and bank statements, club members and administrators get
one place where everything happens automatically.

The system is tightly integrated with [ORIS](https://oris.ceskyorientak.cz/),
the official Czech orienteering information system, so race calendars, entries
and results stay in sync without manual work.

- [📢 &nbsp;Release Notes](https://docs.dalin.cz/changelog/)
- [✨ &nbsp;Used by ABM orienteering club](https://abmbrno.cz)

> [!WARNING]
> Pro dokumentaci v češtině prosím navštivte stránku níže.
> - [🇨🇿 &nbsp;Popis v Češtině](https://github.com/jZejda/dalin/blob/develop/README-CZ.md)

## What DaLin Does

### 🏃 Race entries without the paperwork
Members browse the race calendar (synchronized from ORIS), pick their class and
enter with a few clicks. The club administrator no longer collects entries by
e-mail — DaLin sends them to ORIS automatically and keeps track of deadlines,
changes and cancellations. Relay events are supported too, including building
relay teams from individual members.

### 💰 Transparent member finances
Every member has a credit account in the club. Entry fees are charged against
it automatically, and incoming payments are downloaded straight from the club's
bank account (Fio banka and Moneta connectors are built in) and matched to
members. Everyone can see their own balance and transaction history at any
time; billing specialists see the whole picture.

### 🚗 Shared transport to races
Getting 40 people to a forest on Saturday morning is a logistics problem.
Members can request a seat or offer places in their car for a specific event,
and the system matches requests with offers.

### 🛒 Club marketplace
A simple internal marketplace for club gear — jerseys, SI chips, maps or
second-hand equipment. Orders are paid from the member's club credit.

### 📅 Calendars, exports and notifications
Personal race calendars can be subscribed to via iCal in any calendar app.
Entry lists can be exported in ČSOS and IOF XML formats for organizers.
Members receive e-mail notifications about entry deadlines and important
events.

### 📝 Club content and website
Administrators can publish news posts and static pages, so the system doubles
as a lightweight club website with a members-only section.

### 🔐 Roles for every kind of user
Fine-grained roles (member, racer, event master, billing specialist, club
admin, …) make sure everyone sees exactly what they need — nothing more,
nothing less.

## Who Is It For

- **Members & racers** — enter races, watch their credit, share rides.
- **Club admins & event masters** — manage the calendar, entries and members.
- **Billing specialists** — pair bank payments, oversee club finances.
- **Developers** — a versioned REST API (`/api/v1/`) is available for
  integrations, secured by Sanctum tokens and API keys.

## Show Demo

⚡ This project is used in production by the [ABM Brno](https://abmbrno.cz/) orienteering club.

📘 Documentation on this project may show on [project page](https://jirizejda.cz/dalin/).

![Dalin - Races](https://jirizejda.cz/images/dalin.png)

## Built With

- PHP 8.5, MySQL 8 and up
- [Laravel](https://laravel.com/) 13.x - PHP framework
- [Filament](https://filamentphp.com/) 5.x - admin panel
- [Livewire](https://livewire.laravel.com/) 4 - full-stack framework for dynamic interfaces
- [Alpine.js](https://alpinejs.dev/) - lightweight JavaScript framework
- [Tailwind CSS](https://tailwindcss.com/) 4 - utility-first CSS framework
    - [Flowbite](https://flowbite.com/) - open-source Tailwind CSS library

## Installation

Follow these instructions to install the project for local development:

> [!NOTE]
> For local development use the 🐳 Docker container (Laravel Sail).
> A Makefile with common commands is included — run `make` to see the help.

1. Clone this repo:
    ```bash
    git clone git@gitlab.com:jzejda/dalin.git
    # or
    git clone https://gitlab.com/jzejda/dalin.git
    ```
2. `cd dalin`
3. `cp .env.example .env`
4. `make up` — start the Docker containers
5. `make bash` — enter the PHP container, then inside it:
    1. `composer install`
    2. `php artisan key:generate`
    3. Set the **database config** in the `.env` file
    4. `php artisan migrate --seed` — run migrations and seed data
    5. `php artisan shield:install` → yes and yes
    6. `npm install` — install frontend dependencies
    7. `npm run dev` — compile the assets
6. Open `http://localhost` in your browser.
7. Log in to the admin panel at `http://localhost/admin/login` with credentials
   from `database/seeders/UserTableSeeder.php`.

Useful development services running alongside the app:

| Service | URL | Purpose |
|---|---|---|
| phpMyAdmin | `http://localhost:8084` | Manage the MySQL database |
| Mailpit | `http://localhost:8025` | Catch and preview outgoing e-mails locally |

Common day-to-day commands (run from the project root):

```bash
make pest      # Run the test suite
make lint      # Check code style (Pint)
make phpstan   # Static analysis
make clear     # Clear all caches
```

## License

<p>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

This project is open-sourced software licensed under the [MIT license](LICENSE).
