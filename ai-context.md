# 🧠 Kontext pro AI: Laravel + Filament + Pest

## 📦 Základní informace
- **Název projektu**: Dalin
- **Popis**: Webová aplikace postavená na Laravelu 12, jedná se o klientskou sekci klůbů oddílů orientačních sportů.
- **Cíl**: Modulární, škálovatelný systém s důrazem na typovou bezpečnost, testovatelnost a čistou architekturu.

## 🛠️ Technologie
| Vrstva        | Technologie                         | Poznámky |
|---------------|-------------------------------------|----------|
| Backend       | PHP 8.4 + Laravel 12                | Používáme Eloquent, API Resources, Service Layer |
| Frontend      | TailwindCSS v4.x + FilamentPHP v4.x | Filament slouží jako UI framework pro admin rozhraní |
| Testování     | Pest 4                              | Unit + Feature testy, `expect()` syntaxe |
| Build tools   | Vite                                | Kompilace JS/CSS, hot reload |
| Deployment    | [Forge / Envoyer / jiný]            | CI/CD pipeline, staging & production |

## 🧩 Architektura
- **Modulární struktura**: Každý modul má vlastní `Service`, `Repository`, `DTO`, `Controller`.
- **Validace**: Laravel Form Requests.
- **Autorizace**: Laravel Policies + Filament Permissions.
- **Přístup k datům**: Eloquent ORM, případně query builder pro složité dotazy.

## 🧪 Testovací strategie
- **Framework**: Pest 4
- **Styl**: `describe()`, `it()`, `expect()` syntaxe
- **Mockování**: `Mockery`, Laravel Facades
- **Coverage**: Cíl 80%+ pokrytí klíčových částí

## 🌍 Lokalizace
- **Laravel `lang` soubory**
- **Filament podporuje vícejazyčné UI**
- **Fallback logika**: Pokud překlad chybí, zobrazí se anglický originál

## 📁 Konvence a styly
- **Kód**: PSR-12 + Pint
- **CSS**: Tailwind utility-first
- **Komponenty**: Filament komponenty rozděleny podle domén (`Users`, `Orders`, `Settings`)

## 🧠 AI tipy
- Generuj kód kompatibilní s Laravel 12
- Používej Filament komponenty (`Tables`, `Forms`, `Actions`)
- Testy piš v Pest syntaxi (`expect(...)->toBe(...)`)
- Preferuj typovou bezpečnost (`DTOs`, `strict types`)
- Respektuj modulární strukturu a oddělení 
