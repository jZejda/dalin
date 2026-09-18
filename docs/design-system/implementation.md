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
