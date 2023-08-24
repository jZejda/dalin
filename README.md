# DaLin
DaLin makes it easy to manage orienteering club operations. 
The system uses an external API from the [ORIS](https://oris.orientacnisporty.cz/) system to automatically process members' race entries, download payments, etc.

## Installation Steps

Follow this instructions to install the project for local development:

1. Clone this repo.
    ```bash
    $ git clone git@gitlab.com:jzejda/dalin.git
    # or
    $ git clone https://gitlab.com/jzejda/dalin.git
    ```
2. `$ cd dalin`
3. `$ composer install`
4. `$ cp .env.example .env`
5. `$ php artisan key:generate`
6. Set **database config** on `.env` file
7. `$ php artisan migrate --seed` - run migrations and seeding data
8. `$ php artisan shield:install` -> yes and yes
8. `$ npm install` - install frontend dependencies
9. `$ npm run dev` - compile the assets
10. `$ ./vendor/bin/sail up -d` - start development Sail in docker container
11. Open `https://localhost:8080` with browser.
12. Open `https://localhost:8084` You can manage MySQL database in phpMyAdmin tool.
13. Login into application under `http://localhost:8080/admin/login` with credentilas from `database/seeders/UserTableSeeder.php`

I recommend use `./run.sh` bash tool in root of project.

### Show Demo

This project is presented in [ABM Brno](https://abmbrno.cz/) orienteering club.

## Based on

- PHP v8.2
- MySql 8* and Up
- [Laravel](https://laravel.com/) - PHP framework acctualy in version 10.x
- [Filamentphp](https://filamentphp.com/) - in version 2.x
- [Tailwindcss](https://tailwindcss.com/) - A utility-first CSS framework
  - [FLowbite](https://flowbite.com/) - open-source tailwind css library
- [Livewire](https://laravel-livewire.com/) - Livewire is a full-stack framework for non fronted programmers
- [Alpine.Js](https://alpinejs.dev/) - lightweight, JavaScript framework

## License

<p>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

This project is open-sourced software licensed under the [MIT license](LICENSE).
