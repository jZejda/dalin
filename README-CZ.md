# DaLin

<div align="center">

*PHP - MySQL • Laravel • Filament • Tailwind*

![PHP 8.5](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel 13.x](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![FilamentPHP v5.x](https://img.shields.io/badge/FilamentPHP-v5.x-FB70A9?style=for-the-badge&logo=filament)
![Tailwind CSS 4.0](https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

</div>

**DaLin** je informační systém pro správu klubu orientačních sportů — od
přihlášek na závody přes členské finance až po sdílenou dopravu na závody.
Místo žonglování s tabulkami, e-maily a bankovními výpisy mají členové
i správci klubu jedno místo, kde se vše děje automaticky.

Systém je úzce propojený s [ORISem](https://oris.ceskyorientak.cz/),
oficiálním informačním systémem českého orientačního běhu, takže kalendář
závodů, přihlášky i výsledky zůstávají synchronizované bez ruční práce.

- [📢 &nbsp;Informace k vydání](https://docs.dalin.cz/changelog/)
- [✨ &nbsp;Používá ABM klub orientačního běhu](https://abmbrno.cz)

## Co DaLin umí

### 🏃 Přihlášky na závody bez papírování
Členové si projdou kalendář závodů (synchronizovaný z ORISu), vyberou
kategorii a přihlásí se na pár kliknutí. Správce klubu už nesbírá přihlášky
e-mailem — DaLin je odesílá do ORISu automaticky a hlídá termíny, změny
i odhlášky. Podporované jsou i štafetové závody včetně sestavování
štafetových týmů z jednotlivých členů.

### 💰 Přehledné členské finance
Každý člen má v klubu kreditní účet. Startovné se z něj strhává automaticky
a příchozí platby se stahují přímo z klubového bankovního účtu (vestavěné
jsou konektory pro Fio banku a Monetu) a párují se ke členům. Každý kdykoli
vidí svůj zůstatek a historii transakcí; hospodáři klubu vidí celkový obraz.

### 🚗 Sdílená doprava na závody
Dostat 40 lidí v sobotu ráno do lesa je logistický oříšek. Členové si mohou
k danému závodu vyžádat místo v autě, nebo naopak volná místa ve svém autě
nabídnout, a systém poptávky s nabídkami propojí.

### 🛒 Klubové tržiště
Jednoduché interní tržiště pro klubové vybavení — dresy, SI čipy, mapy nebo
věci z druhé ruky. Objednávky se platí z členského kreditu.

### 📅 Kalendáře, exporty a notifikace
Osobní kalendář závodů lze odebírat přes iCal v libovolné kalendářové
aplikaci. Startovní listiny lze exportovat ve formátech ČSOS a IOF XML pro
pořadatele. Členům chodí e-mailové notifikace o termínech přihlášek
a důležitých událostech.

### 📝 Klubový obsah a web
Správci mohou publikovat novinky a statické stránky, takže systém zároveň
slouží jako jednoduchý klubový web s členskou sekcí.

### 🔐 Role pro každý typ uživatele
Jemně odstupňované role (člen, závodník, správce závodů, hospodář, správce
klubu, …) zajišťují, že každý vidí přesně to, co potřebuje — nic víc, nic míň.

## Pro koho je určen

- **Členové a závodníci** — přihlašují se na závody, sledují svůj kredit,
  sdílejí dopravu.
- **Správci klubu a správci závodů** — spravují kalendář, přihlášky a členy.
- **Hospodáři** — párují bankovní platby, dohlížejí na klubové finance.
- **Vývojáři** — pro integrace je k dispozici verzované REST API
  (`/api/v1/`), zabezpečené Sanctum tokeny a API klíči.

## Ukázka

⚡ Tento projekt v ostrém provozu používá klub [ABM Brno](https://abmbrno.cz/) — Klub orientačního běhu ALFA Brno z.s.

📘 Uživatelskou dokumentaci k projektu naleznete na [stránce nápovědy](https://jirizejda.cz/dalin/).

![Dalin - Závody](https://jirizejda.cz/images/dalin.png)

## Použité technologie

- PHP 8.5, MySQL 8 a vyšší
- [Laravel](https://laravel.com/) 13.x - PHP framework
- [Filament](https://filamentphp.com/) 5.x - administrační panel
- [Livewire](https://livewire.laravel.com/) 4 - full-stack framework pro dynamická rozhraní
- [Alpine.js](https://alpinejs.dev/) - odlehčený JavaScript framework
- [Tailwind CSS](https://tailwindcss.com/) 4 - utility-first CSS framework
    - [Flowbite](https://flowbite.com/) - open-source knihovna pro Tailwind CSS

## Instalace

Pokud chcete projekt lokálně vyvíjet, postupujte podle následujících kroků:

> [!NOTE]
> Pro lokální vývoj používejte 🐳 Docker container (Laravel Sail).
> Součástí projektu je Makefile s běžnými příkazy — nápovědu získáte zadáním `make`.

1. Naklonujte repozitář:
    ```bash
    git clone git@gitlab.com:jzejda/dalin.git
    # nebo
    git clone https://gitlab.com/jzejda/dalin.git
    ```
2. `cd dalin`
3. `cp .env.example .env`
4. `make up` — spustí Docker kontejnery
5. `make bash` — vstup do PHP kontejneru, uvnitř pak:
    1. `composer install`
    2. `php artisan key:generate`
    3. Nastavte **připojení k databázi** v souboru `.env`
    4. `php artisan migrate --seed` — spustí migrace a naplní data
    5. `php artisan shield:install` → yes a yes
    6. `npm install` — instalace frontend závislostí
    7. `npm run dev` — kompilace assetů
6. Otevřete v prohlížeči `http://localhost`.
7. Do administrace se přihlásíte na `http://localhost/admin/login` pomocí
   údajů ze souboru `database/seeders/UserTableSeeder.php`.

Užitečné vývojářské služby běžící vedle aplikace:

| Služba | URL | K čemu slouží |
|---|---|---|
| phpMyAdmin | `http://localhost:8084` | Správa MySQL databáze |
| Mailpit | `http://localhost:8025` | Zachytávání a náhled odchozích e-mailů |

Nejčastější příkazy pro každodenní vývoj (spouštějí se z kořene projektu):

```bash
make pest      # Spustí testy
make lint      # Kontrola stylu kódu (Pint)
make phpstan   # Statická analýza
make clear     # Vyčistí všechny cache
```

## Licence

<p>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

Tento projekt je open-source software licencovaný pod [MIT licencí](LICENSE).
