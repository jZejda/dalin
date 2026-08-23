# Deploy (Deployer 8)

Nasazování DaLinu na sdílený hosting (Webglobe) řídí [Deployer 8](https://deployer.org/docs/8.x).
Konfigurace je v `deploy.php` v rootu projektu — soubor **není v gitu** (repo je veřejné,
`deploy.php` obsahuje hostnames a SSH uživatele produkčních hostingů).

Nahrazuje starý `deploy.sh` (rsync celého pracovního adresáře). Rozdíly:

| | `deploy.sh` (rsync) | Deployer |
|---|---|---|
| Nasazuje | pracovní kopii včetně necommitnutých změn | commit / tag z gitu |
| Přepis souborů | in-place, aplikace je půl minuty rozbitá | nová release, přepnutí symlinku (atomicky) |
| Návrat zpět | ruční | `dep rollback <site>` |
| Vendory | rsyncované z lokálu | `composer install --no-dev` na serveru |
| `.env`, `site-config.php` | ručně vyloučené z rsyncu | `shared/`, deploy se jich nedotkne |

---

## 1. Jak to na serveru vypadá

```
{{deploy_path}}/            např. /home/html/multi_731459/dalin.cz/_sub/demo
├── .dep/                   interní stav Deployeru (zámek, composer.phar)
├── releases/               posledních 5 releasů
│   ├── 1/ 2/ 3/ …
├── shared/                 přežívá releasy
│   ├── .env                per-site konfigurace (DB, klíče)
│   ├── config/
│   │   └── site-config.php per-site nastavení klubu
│   └── storage/            uploady, logy, cache
├── current -> releases/3   symlink na živou release
└── public -> current/public volitelný můstek, když docroot míří na <deploy_path>/public
```

> **Document root domény/subdomény musí v administraci hostingu ukazovat na
> `{{deploy_path}}/current/public`.** Bez toho by byly z webu vidět `releases/`
> a `shared/`.

V každé release jsou `.env`, `config/site-config.php` a `storage/` symlinky do `shared/`.

## 2. Sites

| Alias | Labely | Server | Deploy path | Větev |
|---|---|---|---|---|
| `demo` | `site=dalin stage=demo` | dw303:20001 | `…/dalin.cz/_sub/demo` | `v13.x` |
| `abm` | `site=abm stage=prod` | dw149:20007 | `…/abmbrno.cz/public_html` | `v12.x` |
| `abm-preview` | `site=abm stage=preview` | dw149:20007 | `…/abmbrno.cz/public_html_new` | `v12.x` |
| `abm-staging` | `site=abm stage=staging` | dw149:20007 | `…/abmbrno.cz/_sub/staging` | `v12.x` |
| `pbm` | `site=pbm stage=prod` | dw303:20001 | `…/eob.cz/_sub/pbm-dalin` | `v12.x` |
| `pbm-preview` | `site=pbm stage=preview` | dw303:20001 | `…/eob.cz/_sub/pbm-dalin-preview` | `v12.x` |

Výchozí větev je `v12.x` (na té zatím běží abm i pbm), `demo` má per-host override na
`v13.x`. Jednorázově se přebije přes `--branch=` / `--tag=` / `--revision=`.

Kromě `demo` běží zatím všechny po staru přes `deploy.sh`; převod je stejný postup
jako níže.

## 3. Běžné použití

```bash
vendor/bin/dep deploy demo              # jedna site
vendor/bin/dep deploy site=pbm          # všechny hosty klubu PBM
vendor/bin/dep deploy stage=prod        # všechny produkční
vendor/bin/dep deploy demo --tag=v13.0.1
vendor/bin/dep deploy demo --branch=v13.x
vendor/bin/dep deploy demo --strategy=archive   # kód si vytáhne server z GitHubu

vendor/bin/dep rollback demo            # zpět na předchozí release
vendor/bin/dep releases demo            # historie releasů (datum, číslo, autor, commit)
vendor/bin/dep app:version demo         # nasazená revize
vendor/bin/dep ssh demo                 # shell v current release
vendor/bin/dep logs:app demo            # tail storage/logs
vendor/bin/dep deploy demo --plan       # jen vypíše, co by se stalo
```

Zkratky v `Makefile`: `make deploy s=demo`, `make deploy-tag s=demo t=v13.0.1`,
`make deploy-rollback s=demo`, `make deploy-releases s=demo`, `make deploy-status s=demo`,
`make deploy-logs s=demo`.

### Vydání nové verze (release)

Číslo verze je v repu na jediném místě — `config/version.php` (semver
`MAJOR.MINOR.PATCH`). Git tag je vždy `v<verze>`, tedy `v13.1.0`.

```bash
# 1. bump verze v config/version.php  →  'version' => '13.1.0'
# 2. commit + tag
git commit -am "chore(release): v13.1.0"
git tag -a v13.1.0 -m "v13.1.0"
git push origin v13.x --follow-tags

# 3. deploy tagu
make deploy-tag s=demo t=v13.1.0
```

Číslo sestavení se nikam nepíše ručně — Deployer zapíše do každé release soubor
`REVISION` s plným git SHA nasazeného kódu a `App\Services\AppVersionService`
ho čte za běhu (lokálně, kde `REVISION` není, sáhne do `.git`). Verze a
sestavení jsou pak vidět na třech místech:

- widget **Verze aplikace** dole na úvodní stránce administrace
  (`/admin/user-overview`) — verze, sestavení, datum nasazení, PHP a Laravel
- `php artisan about` — sekce `DaLin`
- `vendor/bin/dep app:version <alias>` — revize nasazená na serveru

Protože `local_archive` deployuje jen to, co je v gitu, musí být bump verze
commitnutý dřív, než se tag nasazuje.

### Přenos kódu

Výchozí strategie je **`local_archive`**: `git archive` z lokálního repa → upload
tar → rozbalení v release. Server nepotřebuje git ani přístup na GitHub.
Nasazuje se to, co je **commitnuté lokálně** (necommitnuté změny deploy hlásí
varováním).

Alternativa `--strategy=archive` nechá server udělat bare mirror
`https://github.com/jZejda/dalin.git`. Vyžaduje na serveru `git` a konektivitu
na github.com. Dá se zapnout natrvalo per-host přes
`->set('update_code_strategy', 'archive')`.

### Vite assety

Na hostingu není Node, assety se staví lokálně (`assets:build`, běží v Sailu —
Vite 8 potřebuje Node 20+, systémový node bývá starší) a nahrávají se do
release (`assets:upload`). Před deployem tedy musí běžet `make up`.

### Co deploy dělá

```
assets:build                    lokálně: sail npm run build
deploy:prepare
  deploy:setup                  vytvoří .dep/ releases/ shared/
  deploy:check_shared           ověří shared/.env (fatální) a site-config.php (varování)
  deploy:lock
  deploy:check_local_target     varuje, když target != lokální HEAD nebo je špinavá kopie
  deploy:release
  deploy:update_code            git archive → upload → rozbalení
  deploy:shared                 symlinky na shared/.env, config/site-config.php, storage/
  deploy:writable               chmod -R storage, bootstrap/cache
deploy:vendors                  composer install --no-dev --optimize-autoloader
assets:upload                   rsync public/build/
artisan:storage:link
artisan:permission:cache-reset
artisan:optimize                config/route/view/event cache
artisan:migrate                 --force
artisan:scribe:generate         API dokumentace (běží z release_path!)
deploy:publish                  přepnutí symlinku current + cleanup starých releasů
```

Chybí tu `artisan:reload` z výchozí Laravel recipe — na sdíleném hostingu neběží
žádní perzistentní workeři (fronta se zpracovává přes `schedule:run`, viz níže).

---

## 4. První deploy demo.dalin.cz — krok za krokem

Cílem je nahradit stávající rsync deploy v `/home/html/multi_731459/dalin.cz/_sub/demo`
strukturou Deployeru. **Demo bude po dobu přepnutí nedostupné** (řádově minuty).

### 4.0 Lokální předpoklady

```bash
make up                                 # Sail běží (kvůli npm build)
vendor/bin/dep --version                # Deployer 8.x
ssh -p 20001 ssh-731459@dw303.webglobe.com 'echo ok'   # SSH klíč funguje bez hesla
```

### 4.1 Preflight na serveru

```bash
ssh -p 20001 ssh-731459@dw303.webglobe.com
```

```bash
php8.4 -v                                   # musí být >= 8.4
php8.4 -m | grep -E 'intl|dom|simplexml|libxml|mbstring|openssl|pdo_mysql|curl|zip|gd|fileinfo|bcmath'
which composer git rsync tar unzip          # composer si Deployer umí stáhnout sám
php8.4 -r 'echo ini_get("memory_limit"), PHP_EOL;'
df -h .                                     # 5 releasů × vendor/ zabere místo
```

Co když něco chybí:

- **composer** — Deployer si při prvním `deploy:vendors` stáhne `composer.phar`
  do `{{deploy_path}}/.dep/`. Nic dělat nemusíš.
- **git** — nevadí, výchozí `local_archive` ho nepotřebuje.
- **unzip** — jen varování, composer bude pomalejší.
- **`php8.4` neexistuje** — zjisti správný název binárky (`ls /usr/bin/php*`)
  a přepiš `set('bin/php', …)` v `deploy.php`, případně per-host.
- **málo místa** — sniž `keep_releases` (výchozí 5).

### 4.2 Databáze

Demo musí mít **vlastní databázi** — `demo:reset` dělá `migrate:fresh`, takže
uživatel té DB nesmí vidět na žádnou ostrou databázi. Když ještě neexistuje,
založ ji v administraci hostingu a poznamenej si credentials.

Pokud přebíráš databázi stávajícího dema, udělej si zálohu:

```bash
mysqldump -h <host> -u <user> -p <db> > ~/dalin-demo-$(date +%F).sql
```

### 4.3 Záloha stávajícího dema

Lokálně (Deployer to najde i ve staré rsync struktuře):

```bash
vendor/bin/dep config:download demo
```

Stáhne `.env` a `config/site-config.php` do `.deploy/demo/`. Adresář `.deploy/`
je v `.gitignore` — **nikdy ho necommituj**, repo je veřejné.

Zálohuj i uploady (`storage/app/public`) — v novém rozložení půjdou do
`shared/storage`:

```bash
rsync -avz -e 'ssh -p 20001' \
  ssh-731459@dw303.webglobe.com:/home/html/multi_731459/dalin.cz/_sub/demo/storage/ \
  ~/Backup/dalin-demo-storage/
```

### 4.4 Úprava per-site konfigurace

Projdi `.deploy/demo/.env` a srovnej ho se šablonou `.env.demo.example`.
Zkontroluj hlavně:

| Klíč | Hodnota |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://demo.dalin.cz` |
| `APP_KEY` | musí být vyplněný — jinak spadne první artisan příkaz |
| `DEMO_MODE` | `true` |
| `DEMO_RESET_URL_KEY` | `openssl rand -hex 24` |
| `DB_*` | vyhrazená demo databáze |
| `MAIL_MAILER` | `log` — demo nesmí posílat maily ven |
| `SESSION_SECURE_COOKIE` | `true` |
| `CRON_URL_KEY`, `CRON_HOURLY_URL_KEY` | vlastní náhodné hodnoty |

Nový `APP_KEY` když chybí: `make art c='key:generate --show'`.

V `.deploy/demo/site-config.php` zkontroluj `club.*` (název klubu, účet, IBAN)
a cron klíče.

### 4.5 Vyčištění deploy path

Až máš zálohu, smaž starý obsah `_sub/demo`. **Od téhle chvíle demo neběží.**

Nemaž ho — **přesuň ho stranou**. Návrat je pak otázka jednoho `mv`, a to je
při prvním deployi k nezaplacení:

```bash
ssh -p 20001 ssh-731459@dw303.webglobe.com
cd /home/html/multi_731459/dalin.cz/_sub
ls -la demo                              # ověř, že jsi u správného adresáře!
mv demo "demo_rsync_backup_$(date +%Y%m%d)"
mkdir demo
```

> Deployer si poradí i s adresářem, ve kterém zbyde balast — jen `current`
> nesmí být adresář. Přesun stranou je ale čistší a dává rollback zadarmo.

### 4.6 Nahrání per-site konfigurace

```bash
vendor/bin/dep config:upload demo
```

Vytvoří `shared/.env` a `shared/config/site-config.php`. Existující soubory
nepřepisuje (pro přepsání je nejdřív smaž na serveru).

Obnov uploady:

```bash
rsync -avz -e 'ssh -p 20001' ~/Backup/dalin-demo-storage/ \
  ssh-731459@dw303.webglobe.com:/home/html/multi_731459/dalin.cz/_sub/demo/shared/storage/
```

### 4.7 První deploy

```bash
git checkout v13.x            # local_archive nasazuje z gitu
vendor/bin/dep deploy demo -v
```

`-v` se vyplatí — u prvního běhu je vidět, kde to případně drhne.

Když deploy spadne uprostřed, zámek se uvolní sám; kdyby přesto zůstal:
`vendor/bin/dep deploy:unlock demo`.

### 4.8 Přepnutí provozu na novou release

Docroot subdomény míří na `_sub/demo/**public**` (ověřeno: `/robots.txt` vrací
200, `/composer.json` a `/artisan` 404). Po přestavbě ten adresář neexistuje,
takže web je do přepnutí dole. Dvě cesty, obě vedou ke stejnému výsledku:

**a) Symlink v deploy path** — funguje okamžitě, bez zásahu v administraci:

```bash
ssh -p 20001 ssh-731459@dw303.webglobe.com
cd /home/html/multi_731459/dalin.cz/_sub/demo
ln -sfn current/public public
```

Docroot pak přes `public` → `current/public` sleduje `current`, takže každý další
deploy se propíše sám a atomicky. Webserver na tomhle hostingu symlinky následuje
(ověřeno). Rollback = `rm public`.

**b) Docroot v administraci Webglobe** — čistší koncový stav; nastav docroot
subdomény `demo.dalin.cz` na:

```
/home/html/multi_731459/dalin.cz/_sub/demo/current/public
```

Po přepnutí v adminu je symlink z varianty a) zbytečný — dá se smazat
(`rm /home/html/multi_731459/dalin.cz/_sub/demo/public`).

Ověř:

```bash
curl -I https://demo.dalin.cz
curl -s https://demo.dalin.cz/admin/login | head -20
```

### 4.9 Naplnění dema daty

```bash
vendor/bin/dep shield:generate demo     # oprávnění Filament Shieldu do DB
vendor/bin/dep demo:reset demo          # migrate:fresh + DemoSeeder
```

`demo:reset` funguje jen s `DEMO_MODE=true`. Přihlašovací údaje (`admin@demo.cz`,
`member@demo.cz`, heslo z `DEMO_ADMIN_PASSWORD`) se zobrazí na login stránce.

### 4.10 Cron

Aplikace nemá systémový scheduler — plánované úlohy se spouštějí HTTP endpointy.
V cronu hostingu nastav:

| Interval | URL |
|---|---|
| každou minutu (nebo co hosting dovolí) | `https://demo.dalin.cz/cron-scheduler/<CRON_URL_KEY>` |
| každou hodinu | `https://demo.dalin.cz/cron-hourly/<CRON_HOURLY_URL_KEY>` |

Klíče jsou ve `shared/config/site-config.php` (resp. v `.env`).
`schedule:run` zajišťuje i zpracování fronty (`queue:work --stop-when-empty`).

Volitelně denní reset dema:

```
https://demo.dalin.cz/demo-reset/<DEMO_RESET_URL_KEY>
```

### 4.11 Kontrolní seznam

- [ ] `https://demo.dalin.cz` odpovídá 200
- [ ] přihlášení do `/admin` funguje
- [ ] obrázky ze `storage` se načítají (symlink `public/storage`)
- [ ] `/api-docs` (Scribe) se vykreslí; `/mcp/dalin` vrací 405 na GET (je POST-only)
- [ ] `storage/logs/laravel.log` bez chyb — `vendor/bin/dep logs:app demo`
- [ ] cron endpointy vrací 200
- [ ] `vendor/bin/dep app:version demo` ukazuje očekávanou revizi

---

## 5. Převod dalších sites

Stejný postup, jen bez demo kroků (4.9):

1. `dep config:download <alias>` — záloha `.env` a `site-config.php`
2. záloha DB a `storage/`
3. vyčištění deploy path (**produkce → domluvit okno**)
4. `dep config:upload <alias>` + obnova `storage/`
5. `dep deploy <alias>`
6. přepnutí docrootu na `…/current/public`

U `abm` je deploy path přímo `public_html`, tedy současný docroot — po převodu
musí docroot ukazovat na `public_html/current/public`.

---

## 6. Řešení potíží

**`Chybí …/shared/.env`**
`dep config:upload <alias>`, nebo soubor vytvoř ručně na serveru.

**`There is a directory (not symlink) at …/current`**
V deploy path zbyl adresář `current` ze staré struktury — smaž ho.

**`deploy locked`**
Předchozí běh spadl tvrdě: `vendor/bin/dep deploy:unlock <alias>`.

**`sail: command not found` / build assetů spadne**
Neběží Sail — `make up`. Kdo má lokálně Node 20+, může v `deploy.php` přepsat
`set('bin/npm_build', 'npm run build')`.

**Composer padá na paměť**
`deploy.php` posílá `COMPOSER_MEMORY_LIMIT=-1`. Když to nestačí, je limit
vynucený hostingem — použij `composer install` s předem vygenerovaným
`vendor/` a nahraj ho (poslední možnost).

**`Class "Laravel\Mcp\Facades\Mcp" not found` (nebo jiná chybějící třída) při
`deploy:vendors`**
Produkční kód závisí na balíčku, který je v `require-dev` — nebo se tam dostává
jen tranzitivně přes dev nástroj. Rsync deploy to maskoval (nahrával lokální
`vendor/` včetně dev balíčků), `composer install --no-dev` to odhalí. Přesuň
balíček do `require` (`composer require <balicek>`) a commitni; `local_archive`
nasazuje commitnutý stav. Přesně tohle potkalo `laravel/mcp`, které si tahal
`laravel/boost`, zatímco na něm stojí `AppServiceProvider` a `routes/ai.php`.
Stejnou past čekej i u `abm`/`pbm` při jejich převodu.

Rychlá diagnostika — Deployer detail composer chyby spolkne, zopakuj ji ručně:

```bash
vendor/bin/dep ssh demo
cd /home/html/.../releases/<N> && php8.4 artisan package:discover
```

**`scribe:generate` spadne**
Nezablokuje web (běží před přepnutím symlinku, takže spadne celý deploy a
`current` zůstane na staré release). Dočasně vyřaď task ze seznamu v `deploy.php`
a řeš odděleně.

**Web ukazuje adresářový výpis `releases/ shared/ current/`**
Docroot ještě míří na deploy path — přepni ho na `…/current/public`.

**Změny v `config/site-config.php` z repa se neprojevily**
Je to shared soubor — edituj `shared/config/site-config.php` na serveru
a pak `vendor/bin/dep artisan:optimize <alias>`.

**Po přidání Filament resource chybí oprávnění**
`vendor/bin/dep shield:generate <alias>`.
