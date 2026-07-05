---
created: 2026-07-03
tags: [oris, api, openapi, orientacni-beh]
---

# ORIS API — OpenAPI specifikace

Neoficiální OpenAPI 3.0.3 specifikace ORIS API (informační systém ČSOS), sestavená z oficiální dokumentace https://oris.orientacnisporty.cz/API.

## Soubory

- `oris-openapi.yaml` — samotná specifikace (38 metod: 37 z dokumentace + `getEventListVersions` zmíněná jen v poznámce u `getEventList`)
- `oris-api-preview.html` — vygenerovaná HTML dokumentace (Redoc), stačí otevřít v prohlížeči
- `redocly.yaml` — konfigurace lintu (vypnutá pravidla s odůvodněním)

## Jak je specifikace postavená

- ORIS je ve skutečnosti jediný endpoint `/API/`, kde metodu určuje query parametr `method`. Pro přehlednost je každá metoda samostatná cesta ve tvaru `/API/?method=<název>` — query string v klíči cesty je mimo striktní OpenAPI standard, ale dokumentační nástroje (Redoc, Swagger UI, Scalar) ho zobrazí správně.
- Metody jsou seskupené do 8 tagů: Závody a kalendář, Přihlášky, Doplňkové služby, Výsledky a startovky, Kluby, Osoby a členství, Správa klubu, Systém a číselníky.
- Všechny parametry mají uvedeno povinné/nepovinné a český popis z dokumentace. GET metody mají parametry v query, POST metody jako `application/x-www-form-urlencoded` tělo.
- Autorizace u POST metod (`username`+`password` NEBO `clubkey`) je vyjádřena přes `anyOf` ve schématu těla + popsána u operace.
- Hlavní formát odpovědi je `json`; `xml` a `xml2` jsou zmíněny u parametru `format` a v odpovědích.
- Odpověď je vždy HTTP 200 se standardní obálkou `{ Method, Format, Status, ExportCreated, Data }` — chyby signalizuje pole `Status`.

## Opravené chyby zdrojové dokumentace

- `createPerson`/`editPerson`: parametr `sisport` uveden 2× — druhý výskyt opraven na `sisport3`.
- `editPerson`: v dokumentaci má chybně popis „vytvoř osobu".
- `stageX` u `createEntry`/`updateEntry` je zástupný název — skutečné parametry jsou `stage1`…`stageN`.

## Validace a regenerace

```bash
npx @redocly/cli lint oris-openapi.yaml          # validace (config redocly.yaml vedle)
npx @redocly/cli build-docs oris-openapi.yaml -o oris-api-preview.html
```
