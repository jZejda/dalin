# DaLin

This is an DaLin management system to make orienteering life better.

## Features

In this project, we have management for czech orienteering clubs.

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
7. `$ php artisan migrate`
8. `$ npm install`
8. `$ npm run prod`
8. `$ php artisan serve`
10. Open `https://localhost:8000` with browser.

### Show Demo

This project is presented in [ABM Brno](https://abmbrno.cz/) orienteering club.

## Documentation

Official documentation in Czech, for this project you can find on [this page](https://oplan.cz/).

## Testing

Run PHPUnit to run feature test:

```bash
$ vendor/bin/phpunit
```

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
