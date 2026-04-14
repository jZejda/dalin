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
