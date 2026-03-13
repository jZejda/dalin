# DaLin

<div align="center">
*PHP - MySQL • Laravel • Filament • Tailwind*

![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel 12.x](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![FilamentPHP v5.x](https://img.shields.io/badge/FilamentPHP-v5.x-FB70A9?style=for-the-badge&logo=filament)
![Tailwind CSS 4.0](https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
</div>

DaLin makes it easy to manage orienteering club operations. 
The system uses an external API from the [ORIS](https://oris.ceskyorientak.cz/) system to automatically process members' race entries, download payments, etc.

- [📢 &nbsp;Release Notes](https://jirizejda.cz/dalin/changelog/)
- [✨ &nbsp;Used by ABM orienteering club](https://abmbrno.cz)

> [!WARNING]
> Pro dokumentaci v češtině prosím navštivte stránku níže.
> - [🇨🇿 &nbsp;Popis v Češtině](https://github.com/jZejda/dalin/blob/develop/README-CZ.md)

## Show Demo

⚡ This project is presented in [ABM Brno](https://abmbrno.cz/) orienteering club.

📘 Documentation on this project may show on [project page](https://jirizejda.cz/dalin/).

![Dalin - Races](https://jirizejda.cz/images/dalin.png)

## Based on

- PHP v8.4
- MySql 8* and Up
- [Laravel](https://laravel.com/) - PHP framework actually in version 12.x
- [Filamentphp](https://filamentphp.com/) - in version 5.x
- [Tailwindcss](https://tailwindcss.com/) - A utility-first CSS framework
    - [Flowbite](https://flowbite.com/) - open-source tailwind css library
- [Livewire](https://livewire.laravel.com/) - Livewire is a full-stack framework for non frontend programmers
- [Alpine.Js](https://alpinejs.dev/) - lightweight, JavaScript framework

## Setup

Follow this instructions to install the project for local development:

> [!NOTE]
> For local development use 🐳 Docker container.
> Just use make file with common commands, using `make` for help

1. Clone this repo.
    ```bash
    git clone git@gitlab.com:jzejda/dalin.git
    # or
    git clone https://gitlab.com/jzejda/dalin.git
    ```
2. `cd dalin`
3. `make up`
4. `make bash`
5. In container image use `composer install`
6. `cp .env.example .env`
7. `php artisan key:generate`
8. Set **database config** on `.env` file
9. `php artisan migrate --seed` - run migrations and seeding data
10. `php artisan shield:install` -> yes and yes
11. `npm install` - install frontend dependencies
12. `npm run dev` - compile the assets
11. Open `https://localhost` with browser.
12. Open `https://localhost:8084` You can manage MySQL database in phpMyAdmin tool.
13. Login into application under `http://localhost/admin/login` with credentials from `database/seeders/UserTableSeeder.php`

I recommend use `make` bash tool in root of project.

## License

<p>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

This project is open-sourced software licensed under the [MIT license](LICENSE).
