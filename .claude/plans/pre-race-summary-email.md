# DEV-9: Přidat mail souhrn před závodem

**Notion:** https://www.notion.so/345bdf3f680c805baf33cd177f276bc1  
**Status:** Analysis

## Cíl

Umožnit uživatelům obdržet den (nebo X dní) před závodem e-mail se souhrnnými informacemi o nadcházejícím závodě a jejich přihláškách.

---

## Datový tok

1. Scheduler spustí job každou hodinu
2. Job najde závody, jejichž `date` je za `days_before` dní
3. Pro každý takový závod najde uživatele s přihláškami (`UserEntry`)
4. Zkontroluje, zda má uživatel v `UserSetting` (type='mail') povoleno `pre_race_summary_enabled = true`
5. Zkontroluje, zda aktuální hodina odpovídá `pre_race_summary_time_trigger`
6. Pokud ano, zašle `PreRaceSummaryMail`

---

## Struktura e-mailu

### Sekce 1 – Informace o akci
- Název akce (`sport_events.name`)
- Datum konání + datum konce (`date`, `date_end`) + počet dní
- Čas startu první etapy (`start_time`)
- Odkaz na ORIS (`oris_id` → `https://oris.orientacnisporty.cz/Zavod?id={oris_id}`)

### Sekce 2 – Závodní profily (přihlášení účastníci)
Jeden řádek na každý `UserEntry`:
- Registrační číslo (`user_race_profiles.reg_number`)
- Jméno a příjmení (`first_name`, `last_name`)
- Kategorie (`user_entries.class_name`)
- Startovní čas přímo hodnota (`real_start`), relativní čás (počet minut mezi `real_start` - `start_time`)
- Parametry kategorie ze `sport_classes`: vzdálenost, počet kontrol, převýšení

### Sekce 3 – Popis akce *(pouze pokud `event_info` není null)*
- `sport_events.event_info`

### Sekce 4 – Linky *(pouze pokud existují záznamy)*
- `sport_event_links`: `name_cz` + `source_url`

### Sekce 5 – Novinky *(pouze pokud existují záznamy)*
- Posledních 5 záznamů z `sport_event_news` (seřazeno `date DESC`)

---

## Co je potřeba vytvořit

### 1. `app/Mail/PreRaceSummaryMail.php`
Nová Mailable třída.
- Konstruktor: `SportEvent $event`, `Collection $entries` (with userRaceProfile + sportClassDefinition loaded)
- Vrátí view `emails.event.preRaceSummary`

### 2. `resources/views/emails/event/preRaceSummary.blade.php`
Blade šablona e-mailu podle vzoru `sportEntryEnds.blade.php`.

### 3. `app/Http/Controllers/Cron/Jobs/ReportEmailPreRaceSummary.php`
Cron job třídy (vzor: `EntryEndsToPay`, `ReportEmailEventWeeklyEndsBySport`).
Implementuje interface `CommonCronJobs` s metodou `run()`.

```php
// Pseudologika run():
$targetDate = now()->addDays($daysBefore)->toDateString();

$events = SportEvent::whereDate('date', $targetDate)
    ->whereHas('userEntry')
    ->with(['userEntry.userRaceProfile.user.userSetting', 
            'userEntry.sportClassDefinition',
            'sportEventNews' => fn($q) => $q->orderByDesc('date')->limit(5),
            'sportEventLinks'])
    ->get();

foreach ($events as $event) {
    // seskupit entries podle user_id
    $entriesByUser = $event->userEntry->groupBy(fn($e) => $e->userRaceProfile->user_id);
    
    foreach ($entriesByUser as $userId => $entries) {
        $user = $entries->first()->userRaceProfile->user;
        $options = $user->getUserOptions('mail');
        
        if (!($options['pre_race_summary_enabled'] ?? false)) continue;
        if ((int)($options['pre_race_summary_time_trigger'] ?? 17) !== now()->hour) continue;
        
        Mail::to($user->email)->send(new PreRaceSummaryMail($event, $entries));
        Log::channel('site')->info('PreRaceSummary mail for user: ' . $user->email);
    }
}
```

### 4. Registrace v `CommonCron::runHourly()` – `app/Http/Controllers/Cron/CommonCron.php`
Přidat nový blok stejného vzoru jako ostatní joby:
```php
/** @description Send Mail pre-race summary */
try {
    if ($this->runJob('mail_pre_race_summary')) {
        Log::channel('site')->info('START MailPreRaceSummary run cron at '.$this->getActualHour());
        new ReportEmailPreRaceSummary()->run();
        Log::channel('site')->info('STOP  MailPreRaceSummary run cron at '.$this->getActualHour());
    }
} catch (Exception $e) {
    Log::channel('site')->warning('ERROR MailPreRaceSummary: '.$e->getMessage());
}
```

### 4b. Nový záznam v `config/site-config.php`
Přidat do sekce `cron_hourly`:
```php
'mail_pre_race_summary' => [
    'active' => true,
    'hours' => ['*'],
    'days_in_month' => ['*'],
    'months' => ['*'],
    'days_in_week' => ['*'],
],
```
> `hours => ['*']` – job se spustí každou hodinu a interní logika rozhodne (podle `pre_race_summary_time_trigger` uživatele), komu mail odeslat.

### 5. `app/Filament/Pages/UserMailNotification.php`
Přidat do sekce „Emailové nastavení" novou podsekci **„Ostatní e-maily"**:

| Klíč v options | Typ | Výchozí | Popis |
|---|---|---|---|
| `pre_race_summary_enabled` | bool | false | Zapnout souhrný mail |
| `pre_race_summary_days_before` | int | 1 | Kolik dní předem |
| `pre_race_summary_time_trigger` | int | 17 | V kolik hodin odeslat |

---

## Soubory ke změně / vytvoření

| Akce | Soubor |
|---|---|
| Vytvořit | `app/Mail/PreRaceSummaryMail.php` |
| Vytvořit | `resources/views/emails/event/preRaceSummary.blade.php` |
| Vytvořit | `app/Http/Controllers/Cron/Jobs/ReportEmailPreRaceSummary.php` |
| Upravit | `app/Http/Controllers/Cron/CommonCron.php` (přidat blok jobu) |
| Upravit | `config/site-config.php` (přidat `mail_pre_race_summary` do `cron_hourly`) |
| Upravit | `app/Filament/Pages/UserMailNotification.php` (nová sekce) |

---

## Vzorové soubory (inspirace)

- `app/Http/Controllers/Cron/Jobs/EntryEndsToPay.php` – vzor pro cron job třídu
- `app/Http/Controllers/Cron/CommonCron.php` – vzor pro registraci v runHourly()
- `app/Mail/EventEntryEnds.php` – vzor pro Mailable
- `resources/views/emails/event/sportEntryEnds.blade.php` – vzor pro šablonu
- `app/Filament/Pages/UserMailNotification.php` – vzor pro settings UI

---

## Poznámky

- `UserRaceProfile` může mít vlastní `email` – pokud není null, použít místo `user->email`
- Multi-day akce: zobrazit rozsah dat `date – date_end` a počet dní
- Startovní čas brát z `user_entries.requested_start`, fallback `real_start`, fallback null
- Parametry kategorie (distance, controls, climbing) jsou na `sport_class_definitions` přes `user_entries.class_definition_id`
