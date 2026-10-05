<?php

declare(strict_types=1);

use App\Enums\SportEventType;
use App\Models\SportEvent;
use App\Models\SportEventExport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Cache::flush();
    Storage::fake('events');
    config(['site-config.club.full_name' => 'OK Testov', 'site-config.club.abbr' => 'TST']);
});

function iofPerson(string $family, string $given): string
{
    return "<Person><Id>TST{$family}</Id><Name><Family>{$family}</Family><Given>{$given}</Given></Name></Person>"
        .'<Organisation><Id>1</Id><Name>OK Testov</Name><ShortName>TST</ShortName></Organisation>';
}

function iofStartListXml(): string
{
    $start = static fn (string $family, string $given, string $time): string => '<PersonStart>'.iofPerson($family, $given)
        ."<Start><StartTime>2026-10-24T{$time}</StartTime><ControlCard>8000001</ControlCard></Start></PersonStart>";

    return '<?xml version="1.0" encoding="UTF-8"?>'
        .'<StartList xmlns="http://www.orienteering.org/datastandard/3.0" iofVersion="3.0" createTime="2026-10-20T18:00:00" creator="Test">'
        .'<Event><Name>14. Jihomoravská liga</Name></Event>'
        .'<ClassStart><Class><Id>1</Id><Name>H21</Name></Class><Course><Name>H21</Name><Length>6200</Length><Climb>180</Climb><NumberOfControls>18</NumberOfControls></Course>'
        .$start('Novák', 'Petr', '10:30:00').$start('Vakant', 'Vakant', '10:32:00').'</ClassStart>'
        .'<ClassStart><Class><Id>2</Id><Name>D21</Name></Class><Course><Name>D21</Name><Length>5100</Length><Climb>140</Climb><NumberOfControls>15</NumberOfControls></Course>'
        .$start('Nováková', 'Jana', '10:31:00').$start('Svobodová', 'Eva', '10:33:00').'</ClassStart>'
        .'</StartList>';
}

function iofResultListXml(): string
{
    $result = static fn (string $family, string $given, int $position): string => '<PersonResult>'.iofPerson($family, $given)
        ."<Result><StartTime>2026-10-24T10:30:00</StartTime><FinishTime>2026-10-24T11:30:00</FinishTime><Time>3600</Time><TimeBehind>0</TimeBehind><Position>{$position}</Position><Status>OK</Status><ControlCard>8000001</ControlCard></Result></PersonResult>";

    return '<?xml version="1.0" encoding="UTF-8"?>'
        .'<ResultList xmlns="http://www.orienteering.org/datastandard/3.0" iofVersion="3.0" createTime="2026-10-24T15:00:00" creator="Test">'
        .'<Event><Name>14. Jihomoravská liga</Name></Event>'
        .'<ClassResult><Class><Name>H21</Name></Class><Course><Name>H21</Name><Length>6200</Length><Climb>180</Climb><NumberOfControls>18</NumberOfControls></Course>'
        .$result('Novák', 'Petr', 1).'</ClassResult>'
        .'</ResultList>';
}

function createListExport(string $type, string $slug, string $xml, ?SportEvent $event = null): SportEventExport
{
    Storage::disk('events')->put('2026/'.$slug.'.xml', $xml);

    return SportEventExport::query()->forceCreate([
        'title' => 'Export '.$slug,
        'slug' => $slug,
        'export_type' => $type,
        'file_type' => SportEventExport::FILE_XML_IOF_V3,
        'sport_event_id' => $event?->id,
        'result_path' => '2026/'.$slug.'.xml',
    ]);
}

it('describes a start list with the event, classes and runners without vacancies', function (): void {
    $event = SportEvent::factory()->create([
        'name' => '14. Jihomoravská liga',
        'event_type' => SportEventType::Race,
        'date' => Carbon::parse('2026-10-24'),
        'place' => 'Brno, Líšeň',
        'discipline_id' => null,
        'level_id' => null,
    ]);
    createListExport(SportEventExport::ENTRY_LIST_CATEGORY, 'startovka-jml-14', iofStartListXml(), $event);

    $html = $this->get('/startovka/startovka-jml-14')
        ->assertOk()
        ->assertSee('<title>Startovka – 14. Jihomoravská liga | TST</title>', escape: false)
        ->assertSee('<meta name="description" content="24.10.2026 · Brno, Líšeň · 2 kategorie · 3 závodníci">', escape: false)
        ->assertSee('<link rel="canonical" href="'.url('/startovka/startovka-jml-14').'">', escape: false)
        ->getContent();

    preg_match('#"@type":"BreadcrumbList".*?</script>#s', (string) $html, $breadcrumbs);
    expect($breadcrumbs[0] ?? '')->toContain(str_replace('/', '\/', route('sport-event.show', $event->id)));
});

it('describes a result list without a linked event', function (): void {
    createListExport(SportEventExport::RESULT_LIST_CATEGORY, 'vysledky-jml-14', iofResultListXml());

    $this->get('/vysledky/vysledky-jml-14')
        ->assertOk()
        ->assertSee('<title>Výsledky – 14. Jihomoravská liga | TST</title>', escape: false)
        ->assertSee('<meta name="description" content="1 kategorie · 1 závodník">', escape: false);
});

it('answers 404 for unknown lists while keeping the friendly message', function (): void {
    $this->get('/startovka/neexistuje')->assertNotFound()->assertSee('Startovka');
    $this->get('/vysledky/neexistuje')->assertNotFound()->assertSee('Výsledky');
});

it('renders a start list whose vacancies have a missing or empty person id', function (string $replacement): void {
    $xml = str_replace('<Person><Id>TSTVakant</Id>', $replacement, iofStartListXml());
    createListExport(SportEventExport::ENTRY_LIST_CATEGORY, 'startovka-vakant', $xml);

    $this->get('/startovka/startovka-vakant')->assertOk()->assertSee('Vakant');
})->with([
    'missing id' => ['<Person>'],
    'empty id' => ['<Person><Id></Id>'],
]);

it('renders a result list whose runner has an empty person id', function (): void {
    $xml = str_replace('<Person><Id>TSTNovák</Id>', '<Person><Id></Id>', iofResultListXml());
    createListExport(SportEventExport::RESULT_LIST_CATEGORY, 'vysledky-bez-id', $xml);

    $this->get('/vysledky/vysledky-bez-id')->assertOk()->assertSee('Novák');
});
