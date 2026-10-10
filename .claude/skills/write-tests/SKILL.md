---
name: write-tests
description: Napíše Pest testy pro zadanou feature, třídu nebo soubor projektu DaLin. Ověří API přes Context7, zkontroluje existující testy a napíše nové podle standardů projektu.
context: fork
agent: test-writer
argument-hint: [cesta k souboru nebo popis feature]
---

Napiš Pest testy pro následující:

$ARGUMENTS

Pokud není zadán konkrétní soubor, analyzuj poslední změny v projektu:
```bash
cd /home/bobik/projects/laravel/dalin && git diff HEAD~1..HEAD --name-only
```

Postupuj podle standardního workflow:
1. Přečti kód který budeš testovat — pochop vstupy, výstupy a hraniční případy
2. Zkontroluj existující testy (`Grep` v `tests/`) — vyhni se duplicitám
3. Ověř aktuální API přes Context7 (Pest, Laravel, případně Filament)
4. Navrhni testovací případy: happy path, edge cases, error cases
5. Napiš testy a po dokončení upozorni na případná rizika nebo závislosti
6. Spusť **celou** sadu Pest testů (`make pest`, příp. `vendor/bin/sail artisan test`), ne jen nově napsané testy

## Před finálním PR musí projít všechny Pest testy

Žádné PR (do `v12.x`/`v13.x` ani jiné cílové větve) se nevytváří a neoznačuje jako hotové, dokud
**celá** sada Pest testů neskončí bez jediného pádu. Platí to bez výjimek, i pro pády,
které už existovaly na cílové větvi:

- Pád, který se objevil už před tvou změnou, se **opraví** (ve vlastním commitu `test:`/`fix:`),
  nikoli obejde přes `->skip()`, `->todo()`, smazání testu nebo zúžení asserce
- Pád, který nejde opravit v rozsahu téhle větve, je blokující — zastav se a řekni to uživateli,
  PR nevytvářej
- Do popisu PR uveď výsledek celé sady (`Tests: N passed`) z posledního běhu
- `--filter` slouží jen k rychlé iteraci během vývoje, finální kontrolu nenahrazuje
