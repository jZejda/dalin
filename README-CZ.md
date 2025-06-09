# DaLin

**DaLin** - informační systém na správu klubu orientačních sportů.
Systém využívá v maximální míře různá napojení na ostatní automatizované systémy, 
pro český region hlavně [ORIS](https://oris.orientacnisporty.cz/) systém pro automatizaci přihlášek na závody, stahování plateb, atd.

- [📢 &nbsp;Informace k vydání](https://jirizejda.cz/dalin/changelog/)
- [✨ &nbsp;Používá ABM klub orientačního běhu](https://abmbrno.cz)

![Dalin - Závody](https://jirizejda.cz/images/dalin.png)

## Show Demo

⚡ Tento projek je využíván klubem [ABM Brno](https://abmbrno.cz/) Klub orientačního běhu ALFA Brno z.s.

📘 Uživatelskou dokumentaci k projektu naleznete na [stránce nápovědy](https://jirizejda.cz/dalin/).

## Používá technologie

- PHP v8.3
- MySql 8* a vyšší
- [Laravel](https://laravel.com/) - PHP framework acctualy ve verzi 11.x
- [Filamentphp](https://filamentphp.com/) - ve verzi 3.x
- [Tailwindcss](https://tailwindcss.com/) - A utility-first CSS framework
    - [FLowbite](https://flowbite.com/) - open-source tailwind css library
- [Livewire](https://laravel-livewire.com/) - Livewire is a full-stack framework for non fronted programmers
- [Alpine.Js](https://alpinejs.dev/) - lightweight, JavaScript framework

## Nastavení

Pokud chcete lokálně vyvíjet tento ptojekt, můžete postupovat podle následujících kroků:

> [!NOTE]
> Pro lokální vývoj prosím používejte 🐳 Docker containter.
> Stačí využívat připravené `make` příkazy pokrývající běžné příkazy při vývoji. Nápovědu získáte prostým zadáním `make` v příkazové řádce projektu

1. Klonování repozitáře.
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
13. Login into application under `http://localhost/admin/login` with credentilas from `database/seeders/UserTableSeeder.php`

Doporučuji používat `make` bash nástroj ve výchozí složce projektu.

## License

<p>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

Tento projekt je open-sourced software využívájící [MIT license](LICENSE).
