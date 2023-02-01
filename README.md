# DaLin
DaLin makes it easy to manage orienteering club operations. 
The system uses an external API from the ORIS system to automatically process members' race entries, download payments, etc.

## Installation Steps

Follow this instructions to install the project:

1. Clone this repo.
    ```bash
    $ git clone git@gitlab.com:jzejda/dalin.git
    # or
    $ git clone https://gitlab.com/jzejda/dalin.git
    ```
2. `$ cd oplan`
3. `$ composer install`
4. `$ cp .env.example .env`
5. `$ php artisan key:generate`
6. Set **database config** on `.env` file
7. `$ php artisan migrate --seed` - run migrations and seeding data
8. `$ npm install` - install frontend dependencies
9. `$ npm run dev` - compile the assets
10. `$ php artisan serve` - start development server
11. Open `https://localhost:8000` with browser.

### Show Demo

This project is presented in [ABM Brno](https://abmbrno.cz/) orienteering club.

## Based on

- PHP v8.1
- MySql 5.6 and Up
- [Laravel](https://laravel.com/) - PHP framework acctualy in version 9.x
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
