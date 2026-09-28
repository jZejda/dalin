# Terrain: zavedení frontendového design systému

## Současná struktura

- `/` vrací `resources/views/welcome.blade.php`: veřejná mapa, Livewire novinky a seznam akcí.
- `/akce/{id}` používá frontendový controller a `pages/frontend/single-event.blade.php`. Šablona již zahrnuje termíny přihlášek, kategorie, mapy, dopravu a další podmíněné sekce.
- `layouts/app.blade.php` skládá Livewire navigaci, patičku a partnery. Načítá také Filament assets pro používané interaktivní prvky.
- `resources/css/app.css` obsahuje styly obsahu a editorových bloků. Filament theme tento soubor importuje, proto není bezpečným místem pro změnu globální frontendové identity.
- Vite sestavuje frontend a admin theme jako samostatné vstupy. Skutečné verze v package.json jsou Tailwind 4 a Vite 8; některé přehledové dokumenty uvádějí starší verze.

## Izolace a postup

1. Samostatný vstup `resources/css/terrain.css` s tokeny přes Tailwind `@theme static`. Vlastní názvy `terrain-*` nepřepisují existující barevné utility. Filament jej neimportuje.
2. Prezentační Blade komponenty `resources/views/components/ui`: tlačítko/odkaz, štítek, záhlaví sekce a vstupní pole. Bez dotazů, autorizace a aplikačního stavu; atributy umožňují budoucí napojení Livewire.
3. `/design-system` je samostatná veřejná showcase s `noindex`, ilustračními daty a vlastním layoutem. Nezávisí na klubových datech ani Filament assets. Existující logo zůstává zachované; text DALIN v showcase je navigační označení, nikoli nové logo.
4. Při redesignu homepage vytvořit frontendový Terrain layout, rozšířit explicitní CSS `@source` a převést veřejnou navigaci/patičku. Zachovat konfiguraci klubů, Livewire data, veřejnou mapu, novinky a partnery. Hero fotografie má být skutečný klubový asset s oprávněním k použití.
5. Detail `/akce/322` převést na stejné komponenty. Zachovat controller, překlady, podmínky přístupů, přihlašování, termíny, dokumenty a Livewire komponenty. Testovat i zrušenou akci, uzávěrku, chybějící mapu a přihlášeného/nepřihlášeného uživatele. Nenahrazovat dynamické hodnoty daty z vizuální reference.
6. Další veřejné seznamy převádět po jednotlivých šablonách. Administrace Filament je mimo rozsah, včetně bodu „Admin UI“ v původním redesign.md.

## Pravidla první verze

- Charcoal text, světlé povrchy, žlutá primární akce. Stavové barvy vždy s textovým označením.
- Systémový sans-serif stack; Inter se použije pouze pokud je lokálně dostupný. Žádné externí načítání fontů.
- Rytmus 4 px, ovládací prvky minimálně 44 px, radius 8/12 px, obsah 72 rem.
- Sekce oddělují linky a prostor; karty patří samostatným objektům.
- Vrstevnice jsou lokální dekorativní SVG, bez síťové závislosti.
- Každá nová či upravovaná frontendová stránka a komponenta musí podporovat světlý i tmavý režim. Terrain přepíná sémantické tokeny přes třídu `dark` na `<html>`, hlavní žlutá akce má stálý tmavý text (`terrain-on-accent`), navigace samostatné tokeny `terrain-nav` / `terrain-on-nav`.
- `<x-ui.theme-script />` patří do head před CSS: respektuje stávající frontendový klíč `color-theme` (light/dark), bez uložené volby sleduje systém včetně jeho změn. Volba „Podle systému“ klíč odstraní. Selhání localStorage neblokuje přepínání. Showcase nabízí všechny tři volby. Preference je společná pro frontend, Filament má vlastní stylování.
- Viditelný keyboard focus, skip link, asociované labely a chybová hlášení. Ukázkové akce navigují na sekce, nepředstírají skutečné přihlášení.

## Ověření

`npm run build`, Pint pro změněné PHP soubory a `sail artisan test tests/Frontend/DesignSystemTest.php tests/Frontend/NewsPageTest.php`. Před převodem existujících šablon ověřit jejich výchozí testy v Sailu. Design system testy kontrolují samostatnou stránku, sémantiku odkazu/tlačítka, zakázaný stav a asociaci labelu/chyby.

Pro chování motivu v prohlížeči spusťte `node tests/Frontend/terrain-theme.cjs` při běžícím Sail webserveru. Test ověřuje obě palety na mobilu i desktopu, prioritu uložené volby, změny systémového motivu, synchronizaci mezi záložkami a nedostupné localStorage. `TERRAIN_TEST_URL` umožňuje změnit adresu showcase.

## Homepage (implementováno)

Homepage používá `layouts/terrain.blade.php` a společné `ui.brand`, `ui.navbar`, `ui.footer` a `ui.theme-select`. Identita a zvýraznění pořadatelského klubu vycházejí z konfigurace. Hero zatím tvoří typografie a vrstevnice; klubová fotografie se doplní po výběru vhodného skutečného assetu.

`PostCards` a `EventList` mají pouze prezentační přepínač `terrain` (výchozí false). Databázové dotazy i filtry zůstaly beze změn, ostatní stránky používají původní šablony. Novinky zobrazují stručný textový perex (nejvýše 240 znaků) a odkaz na celý článek, případně existující `img_url`. Akce odkazují na skutečný detail, zachovávají metadata i ORIS. Prázdné seznamy mají vysvětlující text.

Mapa používá stávající veřejnou Livewire komponentu bez úpravy sdílené Filament šablony. Zůstává s původními kartografickými podklady i v dark mode; tmavé jsou okolní UI povrchy. Partner loga jsou na bílém podkladu kvůli čitelnosti původních barev. Pevná květnová upoutávka na závod už na homepage není.

Ověření: `sail artisan test tests/Frontend`, Pint pro změněné PHP, PHPStan pro obě prezentační Livewire komponenty a `node tests/Frontend/terrain-homepage.cjs`. Browser test pokrývá mobilní menu, Escape, obě palety na šířkách 390/1440 px a inicializaci/zoom mapy. Další etapa: detail `/akce/322`.

## Detail akce (implementováno)

Veřejný detail `/akce/{id}`, včetně `/akce/322`, používá společný Terrain layout a sémantické tokeny v obou motivech. Hero obsahuje skutečný název a podtitul, metadata, štítky a odkazy na existující funkce. Souhrn tvoří jeden pás; dokumenty jsou hned pod ním. Informace, mapa, termíny, služby a novinky jsou v hlavním sloupci, kategorie, doprava, základní údaje a počasí v sidebaru. Běžné sekce mají linky místo karet.

Controller, databázové dotazy, počítání aktivních přihlášek a dopravy, pořadí termínů, příplatky a podmíněná zobrazení zůstaly beze změn. Mapa i marker resolver používají stávající implementaci. Podrobné údaje byly odděleny do prezentačních partialů `pages/frontend/terrain/event-map` a `event-facts`.

Veřejný detail neprovádí registraci ani rezervaci dopravy: odkazuje na stávající Filament entry route generovanou přes `SportEventResource::getUrl`. Autorizaci a pravidla uzávěrek stále řeší původní členská sekce. CTA výslovně označuje správu přihlášek, je použitelné i po uzávěrce a u zrušené akce není zobrazené. Šablony a styly administrace nebyly změněny. Nové texty mají CZ/EN překlady.

Ověření: charakterizační testy detailu prošly ještě před úpravou. `sail artisan test tests/Frontend` pokrývá oba stavy uživatele, zrušení, uzávěrky, chybějící volitelné sekce a zachování dokumentů, kategorií, služeb, novinek, varování, počasí i markerů. `node tests/Frontend/terrain-event.cjs` kontroluje skutečnou akci 322 na 390/1440 px v obou motivech, odkazy, dokumenty a mapový zoom. `TERRAIN_EVENT_URL` umožňuje jinou adresu. Mapové podklady si zachovávají původní kartografické barvy.
