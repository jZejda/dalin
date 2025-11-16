<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>DaLin API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .php-example code { display: none; }
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.5.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.5.0.js") }}"></script>

</head>

<body data-languages="[&quot;php&quot;,&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="php">php</button>
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-calendar" class="tocify-header">
                <li class="tocify-item level-1" data-unique="calendar">
                    <a href="#calendar">Calendar</a>
                </li>
                                    <ul id="tocify-subheader-calendar" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="calendar-ical">
                                <a href="#calendar-ical">ICAL</a>
                            </li>
                                                            <ul id="tocify-subheader-calendar-ical" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="calendar-GETapi-feed-kalendar-zavody-all">
                                            <a href="#calendar-GETapi-feed-kalendar-zavody-all">GET api/feed/kalendar/zavody/all</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="calendar-GETapi-feed-kalendar-treninky-all">
                                            <a href="#calendar-GETapi-feed-kalendar-treninky-all">GET api/feed/kalendar/treninky/all</a>
                                        </li>
                                                                    </ul>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="endpoints">
                    <a href="#endpoints">Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="endpoints-GETapi-user">
                                <a href="#endpoints-GETapi-user">GET api/user</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-v1" class="tocify-header">
                <li class="tocify-item level-1" data-unique="v1">
                    <a href="#v1">V1</a>
                </li>
                                    <ul id="tocify-subheader-v1" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="v1-post">
                                <a href="#v1-post">POST</a>
                            </li>
                                                            <ul id="tocify-subheader-v1-post" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-posts">
                                            <a href="#v1-GETapi-v1-posts">GET api/v1/posts</a>
                                        </li>
                                                                    </ul>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: November 11, 2025</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>Start (and never finish) side projects with this API.</p>
<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="calendar">Calendar</h1>

    <p>APIs for server ical calendar feed common calendar applications</p>

                        <h2 id="calendar-ical">ICAL</h2>
                                        <p>
                    <p>Server ical calendar feed</p>
                </p>
                                        <h2 id="calendar-GETapi-feed-kalendar-zavody-all">GET api/feed/kalendar/zavody/all</h2>

<p>
</p>



<span id="example-requests-GETapi-feed-kalendar-zavody-all">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/feed/kalendar/zavody/all';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/feed/kalendar/zavody/all" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/feed/kalendar/zavody/all"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-feed-kalendar-zavody-all">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: text/calendar; charset=utf-8
content-disposition: attachment; filename=&quot;abm-zavody.ics&quot;
x-ratelimit-limit: 30
x-ratelimit-remaining: 27
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">BEGIN:VCALENDAR
VERSION:2.0
PRODID:spatie/icalendar-generator
NAME:ABC - Kalend&aacute;ř z&aacute;vodů
X-WR-CALNAME:ABC - Kalend&aacute;ř z&aacute;vodů
DESCRIPTION:Kalendař z&aacute;vodů na tento a n&aacute;sledujici rok.
X-WR-CALDESC:Kalendař z&aacute;vodů na tento a n&aacute;sledujici rok.
BEGIN:VTIMEZONE
TZID:Europe/Prague
BEGIN:STANDARD
DTSTART:20241027T030000
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:20250330T010000
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:20251026T030000
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:20260329T010000
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
END:VTIMEZONE
BEGIN:VTIMEZONE
TZID:UTC
BEGIN:STANDARD
DTSTART:20240409T230000
TZOFFSETFROM:+0000
TZOFFSETTO:+0000
END:STANDARD
END:VTIMEZONE
BEGIN:VEVENT
UID:8867
DTSTAMP:20250104T230000Z
SUMMARY:BZL 2 - Sprint v Žabin&aacute;ch
DESCRIPTION:Sportega BZL: Žab&iacute; Sprint | Z&Scaron; Brno\, n&aacute;měst&iacute; Svornosti O
 rganiz&aacute;tor: ZBM - Region: JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250105T130000
DTEND;TZID=Europe/Prague:20250105T170000
GEO:49.215067171267;16.579742431641
END:VEVENT
BEGIN:VEVENT
UID:215
DTSTAMP:20250111T230000Z
SUMMARY:BZL - Klasika na star&eacute; s voln&yacute;m pořad&iacute;m kontrol
DESCRIPTION:N&aacute;vrat ke kořenům | &Uacute;těchov Organiz&aacute;tor: Region: JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250112T100000
DTEND;TZID=Europe/Prague:20250112T140000
GEO:49.2908122;16.6311083
END:VEVENT
BEGIN:VEVENT
UID:8777
DTSTAMP:20250117T230000Z
SUMMARY:BZL - Česk&aacute; obdoba seversk&yacute;ch &scaron;tafet typu Tiomila a Jukola
DESCRIPTION:Kr&aacute;tk&yacute; den 2025 | Dubňany\, SC Želva Organiz&aacute;tor: ZBM - Re
 gion: ČR\, JM\, M -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250118T123000
DTEND;TZID=Europe/Prague:20250118T163000
GEO:48.910118463532;17.09949016571
END:VEVENT
BEGIN:VEVENT
UID:8811
DTSTAMP:20250125T230000Z
SUMMARY:BZL 4 - Městsk&yacute; sprint
DESCRIPTION:Sportega BZL: Poh&aacute;r Legendy | Z&aacute;kladn&iacute; &scaron;kola\, Brno\, Masar
 ova 11 (49.2051661N\, 16.6754636E) Organiz&aacute;tor: ZBM - Region: JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250126T130000
DTEND;TZID=Europe/Prague:20250126T170000
GEO:0;0
END:VEVENT
BEGIN:VEVENT
UID:8810
DTSTAMP:20250222T230000Z
SUMMARY:BZL 5 - Sprint
DESCRIPTION:Sportega BZL: BURE Cup | Brno - Kom&iacute;n\, Středn&iacute; &scaron;kola infor
 matiky\, po&scaron;tovnictv&iacute; a finančnictv&iacute; Brno Organiz&aacute;tor: ZBM - Region: 
 JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250223T130000
DTEND;TZID=Europe/Prague:20250223T170000
GEO:49.214606372965;16.560937464237
END:VEVENT
BEGIN:VEVENT
UID:220
DTSTAMP:20250301T230000Z
SUMMARY:BZL - Městsk&aacute; klasika na vylep&scaron;en&yacute;ch Mapy.cz
DESCRIPTION:Kauflauf | Organiz&aacute;tor: Region: JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250302T100000
DTEND;TZID=Europe/Prague:20250302T140000
GEO:0;0
END:VEVENT
BEGIN:VEVENT
UID:8925
DTSTAMP:20250308T230000Z
SUMMARY:BZL 6 - Sprint
DESCRIPTION:Sportega BZL: Troll cup | Slatina\, Přemyslovo n&aacute;měst&iacute; Orga
 niz&aacute;tor: PBM - Region: JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250309T103000
DTEND;TZID=Europe/Prague:20250309T143000
GEO:49.180048283599;16.685571670532
END:VEVENT
BEGIN:VEVENT
UID:8557
DTSTAMP:20250411T220000Z
SUMMARY:Lucifer Mistrovstv&iacute; ČR a Veterani&aacute;da ČR v nočn&iacute;m OB
DESCRIPTION:Trutnov - Poř&iacute;č&iacute; Organiz&aacute;tor: PHK - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250412T210000
DTEND;TZID=Europe/Prague:20250413T010000
GEO:50.582746123416;15.96156835556
END:VEVENT
BEGIN:VEVENT
UID:8558
DTSTAMP:20250412T220000Z
SUMMARY:Kitl Česk&yacute; poh&aacute;r\, INOV-8 CUP &ndash; žebř&iacute;ček A\,  Mistrovstv&iacute;
  oblasti
DESCRIPTION:Trutnov - Poř&iacute;č&iacute; Organiz&aacute;tor: PHK - Region: ČR\, VČ -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250413T120000
DTEND;TZID=Europe/Prague:20250413T160000
GEO:50.582786997893;15.961761474609
END:VEVENT
BEGIN:VEVENT
UID:8702
DTSTAMP:20250416T220000Z
SUMMARY:Grand prix Silesia
DESCRIPTION:Bohdanovice Organiz&aacute;tor: AOP - Region: MSK -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250417T100000
DTEND;TZID=Europe/Prague:20250417T140000
GEO:49.901401519775;17.643299102783
END:VEVENT
BEGIN:VEVENT
UID:8843
DTSTAMP:20250417T220000Z
SUMMARY:Velikonoce ve skal&aacute;ch
DESCRIPTION:Jestřeb&iacute; Organiz&aacute;tor: DKP - Region: P -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250418T100000
DTEND;TZID=Europe/Prague:20250418T140000
GEO:50.617013724339;14.545555114746
END:VEVENT
BEGIN:VEVENT
UID:8220
DTSTAMP:20250509T220000Z
SUMMARY:Kitl Česk&yacute; poh&aacute;r\, INOV-8 CUP &ndash; žebř&iacute;ček A\, WRE
DESCRIPTION:Jindřichův Hradec Organiz&aacute;tor: SJH - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250510T093000
DTEND;TZID=Europe/Prague:20250510T133000
GEO:49.146499633789;15.00800037384
END:VEVENT
BEGIN:VEVENT
UID:8223
DTSTAMP:20250516T220000Z
SUMMARY:Grant Thornton Mistrovstv&iacute; ČR ve sprintu
DESCRIPTION:Bene&scaron;ov Organiz&aacute;tor: KAM - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250517T130000
DTEND;TZID=Europe/Prague:20250517T170000
GEO:49.782441750999;14.682653536222
END:VEVENT
BEGIN:VEVENT
UID:8497
DTSTAMP:20250523T220000Z
SUMMARY:Žebř&iacute;ček B-Morava\, oblastn&iacute; žebř&iacute;ček
DESCRIPTION:Ho&scaron;tejn\, hři&scaron;tě Organiz&aacute;tor: ASU - Region: HA\, M\, MSK -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250524T120000
DTEND;TZID=Europe/Prague:20250524T160000
GEO:49.87426200876;16.771509647369
END:VEVENT
BEGIN:VEVENT
UID:8499
DTSTAMP:20250524T220000Z
SUMMARY:Žebř&iacute;ček B-Morava\, oblastn&iacute; žebř&iacute;ček
DESCRIPTION:Ho&scaron;tejn Organiz&aacute;tor: ASU - Region: HA\, M -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250525T100000
DTEND;TZID=Europe/Prague:20250525T140000
GEO:49.874227436834;16.771531105042
END:VEVENT
BEGIN:VEVENT
UID:8501
DTSTAMP:20250531T220000Z
SUMMARY:Kitl Česk&yacute; poh&aacute;r\, INOV-8 CUP &ndash; žebř&iacute;ček A\, oblastn&iacute; že
 bř&iacute;ček\, WRE
DESCRIPTION:Řevničov Organiz&aacute;tor: SLA - Region: ČR\, P\, StČ -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250601T100000
DTEND;TZID=Europe/Prague:20250601T140000
GEO:50.150162630272;13.830478191376
END:VEVENT
BEGIN:VEVENT
UID:8500
DTSTAMP:20250530T220000Z
SUMMARY:Kitl Česk&yacute; poh&aacute;r\, INOV-8 CUP &ndash; žebř&iacute;ček A
DESCRIPTION:Řevničov Organiz&aacute;tor: SLA - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250531T120000
DTEND;TZID=Europe/Prague:20250531T160000
GEO:50.150135131067;13.830456733704
END:VEVENT
BEGIN:VEVENT
UID:8503
DTSTAMP:20250613T220000Z
SUMMARY:Žebř&iacute;ček B-Morava\, oblastn&iacute; žebř&iacute;ček
DESCRIPTION:Čavisov Organiz&aacute;tor: MOV - Region: M\, MSK -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250614T120000
DTEND;TZID=Europe/Prague:20250614T160000
GEO:49.829982761806;18.090437613124
END:VEVENT
BEGIN:VEVENT
UID:8505
DTSTAMP:20250614T220000Z
SUMMARY:Žebř&iacute;ček B-Morava\, oblastn&iacute; žebř&iacute;ček
DESCRIPTION:Ostrava - V&yacute;&scaron;kovice Organiz&aacute;tor: MOV - Region: M\, MSK -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250615T100000
DTEND;TZID=Europe/Prague:20250615T140000
GEO:49.777121123028;18.225460468496
END:VEVENT
BEGIN:VEVENT
UID:8596
DTSTAMP:20250620T220000Z
SUMMARY:Grant Thornton Mistrovstv&iacute; ČR na kr&aacute;tk&eacute; trati (kvalifikace)
DESCRIPTION:R&aacute;dlo &ndash; Mil&iacute;ře Organiz&aacute;tor: JJN - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250621T120000
DTEND;TZID=Europe/Prague:20250621T160000
GEO:50.712112896185;15.110793113708
END:VEVENT
BEGIN:VEVENT
UID:8225
DTSTAMP:20250626T220000Z
SUMMARY:Veterani&aacute;da ČR ve sprintu
DESCRIPTION:Třebechovice pod Orebem Organiz&aacute;tor: SHK - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250627T173000
DTEND;TZID=Europe/Prague:20250627T213000
GEO:50.199608099406;15.987038387029
END:VEVENT
BEGIN:VEVENT
UID:8226
DTSTAMP:20250627T220000Z
SUMMARY:Veterani&aacute;da ČR na klasick&eacute; trati\, oblastn&iacute; žebř&iacute;ček
DESCRIPTION:Bělečko Organiz&aacute;tor: SHK - Region: ČR\, VČ -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250628T100000
DTEND;TZID=Europe/Prague:20250628T140000
GEO:50.152225025564;15.951633083586
END:VEVENT
BEGIN:VEVENT
UID:8227
DTSTAMP:20250628T220000Z
SUMMARY:Veterani&aacute;da ČR na kr&aacute;tk&eacute; trati
DESCRIPTION:Bělečko Organiz&aacute;tor: SHK - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250629T100000
DTEND;TZID=Europe/Prague:20250629T140000
GEO:50.152238774568;15.951772965794
END:VEVENT
BEGIN:VEVENT
UID:8711
DTSTAMP:20250729T220000Z
SUMMARY:Bohemia orienteering 2025
DESCRIPTION:Nov&yacute; Bor Organiz&aacute;tor: BOR - Region: JE -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250730T100000
DTEND;TZID=Europe/Prague:20250730T140000
GEO:50.753698956938;14.54448223114
END:VEVENT
BEGIN:VEVENT
UID:8228
DTSTAMP:20250905T220000Z
SUMMARY:Kitl Česk&yacute; poh&aacute;r\, INOV-8 CUP &ndash; žebř&iacute;ček A\, žebř&iacute;ček 
 B-Morava\, oblastn&iacute; žebř&iacute;ček\, WRE
DESCRIPTION:Sobot&iacute;n\, sedlo Skř&iacute;tek Organiz&aacute;tor: KSU - Region: ČR\, HA
 \, M\, MSK -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250906T120000
DTEND;TZID=Europe/Prague:20250906T160000
GEO:49.998360556132;17.15446472168
END:VEVENT
BEGIN:VEVENT
UID:8229
DTSTAMP:20250906T220000Z
SUMMARY:Kitl Česk&yacute; poh&aacute;r\, INOV-8 CUP &ndash; žebř&iacute;ček A\, žebř&iacute;ček 
 B-Morava\, oblastn&iacute; žebř&iacute;ček
DESCRIPTION:Sobot&iacute;n\, sedlo Skř&iacute;tek Organiz&aacute;tor: KSU - Region: ČR\, HA
 \, M -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250907T100000
DTEND;TZID=Europe/Prague:20250907T140000
GEO:49.998595039894;17.154057025909
END:VEVENT
BEGIN:VEVENT
UID:8620
DTSTAMP:20250912T220000Z
SUMMARY:Grant Thornton Mistrovstv&iacute; ČR na klasick&eacute; trati (kvalifikace)
DESCRIPTION:Horn&iacute; Lhota (okr. Zl&iacute;n) Organiz&aacute;tor: LCE - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250913T120000
DTEND;TZID=Europe/Prague:20250913T160000
GEO:49.155215079432;17.792959213257
END:VEVENT
BEGIN:VEVENT
UID:8230
DTSTAMP:20250919T220000Z
SUMMARY:Kitl Česk&yacute; poh&aacute;r\, INOV-8 CUP &ndash; žebř&iacute;ček A
DESCRIPTION:Kl&aacute;&scaron;ter Hradi&scaron;tě nad Jizerou Organiz&aacute;tor: TUR - Region: Č
 R -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250920T093000
DTEND;TZID=Europe/Prague:20250920T133000
GEO:50.530711420799;14.941298961639
END:VEVENT
BEGIN:VEVENT
UID:8509
DTSTAMP:20251003T220000Z
SUMMARY:Grant Thornton Mistrovstv&iacute; a Veterani&aacute;da ČR &scaron;tafet
DESCRIPTION:V&yacute;&scaron;ice Organiz&aacute;tor: TAP - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20251004T110000
DTEND;TZID=Europe/Prague:20251004T150000
GEO:49.452057117544;14.004650115967
END:VEVENT
BEGIN:VEVENT
UID:8510
DTSTAMP:20251004T220000Z
SUMMARY:Grant Thornton Mistrovstv&iacute; a Veterani&aacute;da ČR klubů\, Mistrovstv
 &iacute; ČR oblastn&iacute;ch v&yacute;běrů žactva
DESCRIPTION:V&yacute;&scaron;ice Organiz&aacute;tor: TAP - OPI - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20251005T093000
DTEND;TZID=Europe/Prague:20251005T133000
GEO:49.452336102579;14.004564285278
END:VEVENT
BEGIN:VEVENT
UID:8885
DTSTAMP:20250717T220000Z
SUMMARY:GAPP Czech O-Tour 2025 - Moravsk&yacute; kras
DESCRIPTION:Babice nad Svitavou\; Brno Organiz&aacute;tor: CSOS - Region: ČR\, J
 M -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250718T100000
DTEND;TZID=Europe/Prague:20250718T140000
GEO:49.294204601011;16.694412046554
END:VEVENT
BEGIN:VEVENT
UID:8888
DTSTAMP:20251024T220000Z
SUMMARY:GAPP Czech O-Tour 2025 - Kru&scaron;n&eacute; hory
DESCRIPTION:Lesn&aacute;\, Kru&scaron;n&eacute; hory Organiz&aacute;tor: CSOS - Region: ČR\, JE -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20251025T100000
DTEND;TZID=Europe/Prague:20251025T140000
GEO:50.564676130782;13.430957759522
END:VEVENT
BEGIN:VEVENT
UID:8966
DTSTAMP:20250404T220000Z
SUMMARY:1. JML
DESCRIPTION:Oblastn&iacute; žebř&iacute;ček | Tetčice\, Sokolovna - venkovn&iacute; prost
 ory Organiz&aacute;tor: ZBM - Region: JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250405T103000
DTEND;TZID=Europe/Prague:20250405T143000
GEO:49.171440499225;16.404886553862
END:VEVENT
BEGIN:VEVENT
UID:8967
DTSTAMP:20250411T220000Z
SUMMARY:2. JML
DESCRIPTION:Oblastn&iacute; žebř&iacute;ček | Radostice Organiz&aacute;tor: BBM - Region: 
 JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250412T103000
DTEND;TZID=Europe/Prague:20250412T143000
GEO:49.132798386266;16.478880571621
END:VEVENT
BEGIN:VEVENT
UID:8968
DTSTAMP:20250425T220000Z
SUMMARY:3. JML
DESCRIPTION:Mistrovstv&iacute; oblasti ve sprintu | Mokr&aacute;-Hor&aacute;kov\, koupali&scaron;t
 ě Mokr&aacute; Organiz&aacute;tor: TBM - Region: JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250426T103000
DTEND;TZID=Europe/Prague:20250426T143000
GEO:49.220470280266;16.750481088248
END:VEVENT
BEGIN:VEVENT
UID:8969
DTSTAMP:20250507T220000Z
SUMMARY:4. JML
DESCRIPTION:Mistrovstv&iacute; oblasti na kr&aacute;tk&eacute; trati | Z&Scaron; Pavlovsk&aacute;\, Brno 
 - Kohoutovice Organiz&aacute;tor: LBM - Region: JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250508T103000
DTEND;TZID=Europe/Prague:20250508T143000
GEO:49.190539023835;16.527117490768
END:VEVENT
BEGIN:VEVENT
UID:8989
DTSTAMP:20250516T220000Z
SUMMARY:5. JML
DESCRIPTION:Mistrovstv&iacute; oblasti na kr&aacute;tk&eacute; trati | Pohora &ndash; louka\, GPS
 : 49.5617644N\, 16.7623325E &ndash; velk&yacute; stan + klubov&eacute; stany Organiz&aacute;tor:
  KON - Region: HA\, JM\, VA -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250517T110000
DTEND;TZID=Europe/Prague:20250517T150000
GEO:49.561589870843;16.762241053919
END:VEVENT
BEGIN:VEVENT
UID:8970
DTSTAMP:20250530T220000Z
SUMMARY:6. JML
DESCRIPTION:Oblastn&iacute; žebř&iacute;ček | Jedovnice\, kemp Ol&scaron;ovec Organiz&aacute;tor
 : ADA - Region: JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250531T103000
DTEND;TZID=Europe/Prague:20250531T143000
GEO:49.331072410465;16.763645410538
END:VEVENT
BEGIN:VEVENT
UID:8971
DTSTAMP:20250606T220000Z
SUMMARY:7. JML
DESCRIPTION:Mistrovstv&iacute; oblasti na klasick&eacute; trati | Bunkr - b&yacute;val&yacute; voje
 nsk&yacute; are&aacute;l\, Mokr&aacute; - Hor&aacute;kov Organiz&aacute;tor: PBM - Region: JM\, VY -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250607T103000
DTEND;TZID=Europe/Prague:20250607T143000
GEO:49.219391107258;16.731834411621
END:VEVENT
BEGIN:VEVENT
UID:8972
DTSTAMP:20250620T220000Z
SUMMARY:8. JML
DESCRIPTION:Oblastn&iacute; žebř&iacute;ček | Oře&scaron;&iacute;n Organiz&aacute;tor: VBM - Region: 
 JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250621T103000
DTEND;TZID=Europe/Prague:20250621T143000
GEO:49.282703559295;16.610040664673
END:VEVENT
BEGIN:VEVENT
UID:8973
DTSTAMP:20250624T220000Z
SUMMARY:Mistrovstv&iacute; oblasti &scaron;tafet
DESCRIPTION:Brno\, L&iacute;&scaron;eň (Tenis u lomu) Organiz&aacute;tor: PBM - Region: JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250625T171500
DTEND;TZID=Europe/Prague:20250625T211500
GEO:49.21824183217;16.694326400757
END:VEVENT
BEGIN:VEVENT
UID:8974
DTSTAMP:20250912T220000Z
SUMMARY:Oblastn&iacute; žebř&iacute;ček - Jihomoravsk&aacute; oblast
DESCRIPTION:9. Jihomoravsk&aacute; liga | Z&Scaron; Pavlovsk&aacute;\, Brno-Kohoutovice\, Org
 aniz&aacute;tor: ZBM - Region: JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250913T103000
DTEND;TZID=Europe/Prague:20250913T143000
GEO:49.190595118072;16.526978011566
END:VEVENT
BEGIN:VEVENT
UID:8992
DTSTAMP:20250926T220000Z
SUMMARY:Oblastn&iacute; žebř&iacute;ček - Jihomoravsk&aacute; oblast
DESCRIPTION:10. Jihomoravsk&aacute; liga | Ochoz u Brna Organiz&aacute;tor: RBK - Regio
 n: JM\, VA -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250927T103000
DTEND;TZID=Europe/Prague:20250927T143000
GEO:49.250618029959;16.761918067932
END:VEVENT
BEGIN:VEVENT
UID:8993
DTSTAMP:20251010T220000Z
SUMMARY:Oblastn&iacute; žebř&iacute;ček - Jihomoravsk&aacute; oblast
DESCRIPTION:11. Jihomoravsk&aacute; liga | Vacenovice Organiz&aacute;tor: TZL - Region:
  JM\, VA -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20251011T110000
DTEND;TZID=Europe/Prague:20251011T150000
GEO:48.93795;17.18087
END:VEVENT
BEGIN:VEVENT
UID:8976
DTSTAMP:20251017T220000Z
SUMMARY:Oblastn&iacute; žebř&iacute;ček - Jihomoravsk&aacute; oblast
DESCRIPTION:12. Jihomoravsk&aacute; liga | Fry&scaron;ava pod Ž&aacute;kovou horou\, hři&scaron;t
 ě u Obecn&iacute;ho rybn&iacute;ku Organiz&aacute;tor: PBM - Region: JM\, VY -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20251018T103000
DTEND;TZID=Europe/Prague:20251018T143000
GEO:49.646235836432;16.040296554565
END:VEVENT
BEGIN:VEVENT
UID:8982
DTSTAMP:20251018T220000Z
SUMMARY:Oblastn&iacute; žebř&iacute;ček - Jihomoravsk&aacute; oblast
DESCRIPTION:13. Jihomoravsk&aacute; liga | obec S&aacute;zava\, koupali&scaron;tě Organiz&aacute;t
 or: PZR - Region: JM\, VY -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20251019T103000
DTEND;TZID=Europe/Prague:20251019T143000
GEO:49.555131447312;15.876338481903
END:VEVENT
BEGIN:VEVENT
UID:8965
DTSTAMP:20250123T230000Z
SUMMARY:&Scaron;kolen&iacute; tren&eacute;rů OB 3. tř&iacute;dy
DESCRIPTION:střed Moravy a Slezska - bude upřesněno podle přihl&aacute;&scaron;ek O
 rganiz&aacute;tor: JMO - Region: HA\, JM\, MSK\, VA -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250124T100000
DTEND;TZID=Europe/Prague:20250124T140000
GEO:49.38544589543;17.12287902832
END:VEVENT
BEGIN:VEVENT
UID:8788
DTSTAMP:20250124T230000Z
SUMMARY:&Scaron;kolen&iacute; rozhodč&iacute;ch 2. tř&iacute;dy
DESCRIPTION:Hradec Kr&aacute;lov&eacute; Organiz&aacute;tor: CSOS - SHK - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250125T100000
DTEND;TZID=Europe/Prague:20250125T140000
GEO:50.184895244202;15.845009558376
END:VEVENT
BEGIN:VEVENT
UID:8889
DTSTAMP:20250125T230000Z
SUMMARY:GAPP Czech O-Tour 2025 - seri&aacute;l
DESCRIPTION:Organiz&aacute;tor: CSOS - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250126T100000
DTEND;TZID=Europe/Prague:20250126T140000
GEO:0;0
END:VEVENT
BEGIN:VEVENT
UID:8878
DTSTAMP:20250125T230000Z
SUMMARY:GAPP Czech O-Tour 2025 - ZOO Praha
DESCRIPTION:ZOO Praha\, 50.1167878N\, 14.4111319E Organiz&aacute;tor: CSOS - TAP 
 - Region: ČR\, P -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250126T090000
DTEND;TZID=Europe/Prague:20250126T130000
GEO:50.116615600395;14.411377529167
END:VEVENT
BEGIN:VEVENT
UID:8956
DTSTAMP:20250124T230000Z
SUMMARY:BZL 2. Sportega
DESCRIPTION:Sportega BZL: Hromničn&iacute; tr&aacute;pen&iacute; 2025 | Blansko Organiz&aacute;tor
 : RBK - Region: JM -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250125T103000
DTEND;TZID=Europe/Prague:20250125T143000
GEO:49.365495202837;16.655766983258
END:VEVENT
BEGIN:VEVENT
UID:8912
DTSTAMP:20250125T230000Z
SUMMARY:Zimn&iacute; hradubick&aacute; liga - Led(n)ov&yacute; Hradec
DESCRIPTION:Hradec Kr&aacute;lov&eacute;\, are&aacute;l firem GEO&Scaron;RAFO a Geod&eacute;zie V&yacute;chodn
 &iacute; Čechy (Kladsk&aacute; 181) Organiz&aacute;tor: SHK - Region: VČ -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250126T100000
DTEND;TZID=Europe/Prague:20250126T140000
GEO:50.223652686061;15.874365017411
END:VEVENT
BEGIN:VEVENT
UID:8979
DTSTAMP:20250328T230000Z
SUMMARY:1. z&aacute;vod Han&aacute;ck&eacute;ho žebř&iacute;čku
DESCRIPTION:Oblastn&iacute; žebř&iacute;ček | Určice Organiz&aacute;tor: JPV - Region: HA
  -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250329T110000
DTEND;TZID=Europe/Prague:20250329T150000
GEO:49.424506503148;17.062089443207
END:VEVENT
BEGIN:VEVENT
UID:9236
DTSTAMP:20250307T230000Z
SUMMARY:13. ročn&iacute;k Zl&iacute;nsk&eacute;ho Orientačn&iacute;ho Maratonu
DESCRIPTION:Veřejn&yacute; z&aacute;vod (z&aacute;vod na 50 kontrol + ZLOM) | Vacenovice Org
 aniz&aacute;tor: TZL - Region: VA -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250308T110000
DTEND;TZID=Europe/Prague:20250308T150000
GEO:48.937949806527;17.180857658386
END:VEVENT
BEGIN:VEVENT
UID:8881
DTSTAMP:20250404T220000Z
SUMMARY:GAPP Czech O-Tour 2025 - Žacl&eacute;ř
DESCRIPTION:Žacl&eacute;ř\, 50.6690397N\, 15.9271631E Organiz&aacute;tor: CSOS - LTU 
 - Region: ČR\, VČ -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250405T100000
DTEND;TZID=Europe/Prague:20250405T140000
GEO:50.669157146435;15.926957084209
END:VEVENT
BEGIN:VEVENT
UID:9182
DTSTAMP:20250411T220000Z
SUMMARY:Česk&yacute; poh&aacute;r 3. kolo\, Mistrovstv&iacute; ČR (MASS START)
DESCRIPTION:Lhota u Star&eacute; Boleslavi Organiz&aacute;tor: SPC - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250412T093000
DTEND;TZID=Europe/Prague:20250412T133000
GEO:50.24564060447;14.668722199378
END:VEVENT
BEGIN:VEVENT
UID:9183
DTSTAMP:20250411T220000Z
SUMMARY:Česk&yacute; poh&aacute;r 4. kolo
DESCRIPTION:Lhota u Star&eacute; Boleslavi Organiz&aacute;tor: SPC - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250412T160000
DTEND;TZID=Europe/Prague:20250412T200000
GEO:50.245668048719;14.668722356883
END:VEVENT
BEGIN:VEVENT
UID:9184
DTSTAMP:20250412T220000Z
SUMMARY:Česk&yacute; poh&aacute;r 5. kolo
DESCRIPTION:Lhota u Star&eacute; Boleslavi Organiz&aacute;tor: SPC - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250413T100000
DTEND;TZID=Europe/Prague:20250413T140000
GEO:50.245640604442;14.668700730039
END:VEVENT
BEGIN:VEVENT
UID:9037
DTSTAMP:20250411T220000Z
SUMMARY:Mistrovstv&iacute; oblasti na kr&aacute;tk&eacute; trati
DESCRIPTION:Kostelec Organiz&aacute;tor: SJI - Region: VY -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250412T100000
DTEND;TZID=Europe/Prague:20250412T140000
GEO:49.360066039567;15.495904684067
END:VEVENT
BEGIN:VEVENT
UID:9026
DTSTAMP:20250821T220000Z
SUMMARY:Pěkn&eacute; pr&aacute;zdniny s orientačn&iacute;m během v Česk&eacute;m r&aacute;ji
DESCRIPTION:Na Pra&scaron;ivci\, Hrub&aacute; Sk&aacute;la Organiz&aacute;tor: TUR - Region: JE -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250822T100000
DTEND;TZID=Europe/Prague:20250822T140000
GEO:50.537599057873;15.207565964253
END:VEVENT
BEGIN:VEVENT
UID:9154
DTSTAMP:20250828T220000Z
SUMMARY:West cup
DESCRIPTION:R&aacute;jec Organiz&aacute;tor: NEK - Region: ZČ -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250829T160000
DTEND;TZID=Europe/Prague:20250829T200000
GEO:50.287748919846;12.728510030493
END:VEVENT
BEGIN:VEVENT
UID:9166
DTSTAMP:20250710T220000Z
SUMMARY:H.S.H. Vysočina cup
DESCRIPTION:Pust&aacute; Kamenice Organiz&aacute;tor: CHT - Region: VY -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250711T100000
DTEND;TZID=Europe/Prague:20250711T140000
GEO:49.748526601892;16.100120786806
END:VEVENT
BEGIN:VEVENT
UID:9039
DTSTAMP:20250425T220000Z
SUMMARY:6. LV
DESCRIPTION:Mistrovstv&iacute; oblasti na klasick&eacute; trati | Včel&aacute;kov Organiz&aacute;t
 or: CHT - Region: VY -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250426T103000
DTEND;TZID=Europe/Prague:20250426T143000
GEO:49.813134794608;15.885071869871
END:VEVENT
BEGIN:VEVENT
UID:8929
DTSTAMP:20250703T220000Z
SUMMARY:Cena středn&iacute; Moravy
DESCRIPTION:Velenov\, louka Organiz&aacute;tor: KON - Region: HA -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250704T100000
DTEND;TZID=Europe/Prague:20250704T140000
GEO:49.489092435122;16.723766326904
END:VEVENT
BEGIN:VEVENT
UID:9188
DTSTAMP:20250606T220000Z
SUMMARY:Česk&yacute; poh&aacute;r 9. kolo
DESCRIPTION:Polička Organiz&aacute;tor: TSU - XPU - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250607T093000
DTEND;TZID=Europe/Prague:20250607T133000
GEO:49.683346325308;16.329288482666
END:VEVENT
BEGIN:VEVENT
UID:9189
DTSTAMP:20250607T220000Z
SUMMARY:Česk&yacute; poh&aacute;r 10. kolo
DESCRIPTION:Polička Organiz&aacute;tor: TSU - XPU - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250608T100000
DTEND;TZID=Europe/Prague:20250608T140000
GEO:49.68329079184;16.329352855682
END:VEVENT
BEGIN:VEVENT
UID:9190
DTSTAMP:20250613T220000Z
SUMMARY:Česk&yacute; poh&aacute;r 11. kolo
DESCRIPTION:Kirchbach (Zwettl) Organiz&aacute;tor: 15A - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250614T130000
DTEND;TZID=Europe/Prague:20250614T170000
GEO:48.539710432977;15.0430727005
END:VEVENT
BEGIN:VEVENT
UID:9191
DTSTAMP:20250614T220000Z
SUMMARY:Česk&yacute; poh&aacute;r 12. kolo
DESCRIPTION:Kirchbach (Zwettl) Organiz&aacute;tor: 15A - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250615T100000
DTEND;TZID=Europe/Prague:20250615T140000
GEO:48.539653604377;15.043201446533
END:VEVENT
BEGIN:VEVENT
UID:9213
DTSTAMP:20250630T220000Z
SUMMARY:MTBO5days Plzeň 2025 (E1-E5)
DESCRIPTION:Ejpovice\, Plzeň Organiz&aacute;tor: VPM - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250701T100000
DTEND;TZID=Europe/Prague:20250701T140000
GEO:49.753074022668;13.512325309361
END:VEVENT
BEGIN:VEVENT
UID:9413
DTSTAMP:20250623T220000Z
SUMMARY:Veřejn&yacute; z&aacute;vod (Akademick&eacute; MČR)
DESCRIPTION:FAST V&Scaron;B-TU Organiz&aacute;tor: AOV - Region: ČR\, MSK -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250624T170000
DTEND;TZID=Europe/Prague:20250624T210000
GEO:49.84529;18.15295
END:VEVENT
BEGIN:VEVENT
UID:9414
DTSTAMP:20250624T220000Z
SUMMARY:Veřejn&yacute; z&aacute;vod (Akademick&eacute; MČR)
DESCRIPTION:lesopark u V&Scaron;B-TU Ostrava Organiz&aacute;tor: AOV - Region: ČR\, MS
 K -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250625T100000
DTEND;TZID=Europe/Prague:20250625T140000
GEO:49.83474;18.15947
END:VEVENT
BEGIN:VEVENT
UID:8964
DTSTAMP:20250702T220000Z
SUMMARY:Orienteering Adventure
DESCRIPTION:&Scaron;ediviny Organiz&aacute;tor: OAV - Region: VČ -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250703T100000
DTEND;TZID=Europe/Prague:20250703T140000
GEO:50.304170665784;16.3145261152
END:VEVENT
BEGIN:VEVENT
UID:9021
DTSTAMP:20250828T220000Z
SUMMARY:Cena v&yacute;chodn&iacute;ch Čech
DESCRIPTION:V&yacute;&scaron;inka Organiz&aacute;tor: PHK - Region: VČ -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250829T100000
DTEND;TZID=Europe/Prague:20250829T140000
GEO:50.480831312986;15.864515304565
END:VEVENT
BEGIN:VEVENT
UID:9195
DTSTAMP:20250905T220000Z
SUMMARY:Česk&yacute; poh&aacute;r 16. kolo\, HAVEN Mistrovstv&iacute; ČR (LONG)
DESCRIPTION:Ro&scaron;t&iacute;n Organiz&aacute;tor: ABR - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250906T120000
DTEND;TZID=Europe/Prague:20250906T160000
GEO:49.185854930707;17.307736716878
END:VEVENT
BEGIN:VEVENT
UID:9196
DTSTAMP:20250906T220000Z
SUMMARY:Česk&yacute; poh&aacute;r 17. kolo\, HAVEN Mistrovstv&iacute; ČR (MIDDLE)
DESCRIPTION:Ro&scaron;t&iacute;n Organiz&aacute;tor: ABR - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250907T100000
DTEND;TZID=Europe/Prague:20250907T140000
GEO:49.185854930707;17.308363552024
END:VEVENT
BEGIN:VEVENT
UID:9197
DTSTAMP:20250926T220000Z
SUMMARY:Česk&yacute; poh&aacute;r 18. kolo
DESCRIPTION:Tuž&iacute;n Organiz&aacute;tor: SJC - Region: ČR -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250927T143000
DTEND;TZID=Europe/Prague:20250927T183000
GEO:50.46646481021;15.438108444214
END:VEVENT
BEGIN:VEVENT
UID:9043
DTSTAMP:20250926T220000Z
SUMMARY:Oblastn&iacute; žebř&iacute;ček
DESCRIPTION:Ple&scaron;ice Organiz&aacute;tor: TTR - Region: VY -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250927T103000
DTEND;TZID=Europe/Prague:20250927T143000
GEO:49.165500623537;16.039148567498
END:VEVENT
BEGIN:VEVENT
UID:8833
DTSTAMP:20250926T220000Z
SUMMARY:Oblastn&iacute; žebř&iacute;ček
DESCRIPTION:Tuž&iacute;n Organiz&aacute;tor: SJC - Region: VČ -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20250927T100000
DTEND;TZID=Europe/Prague:20250927T140000
GEO:50.466929195111;15.437550544739
END:VEVENT
BEGIN:VEVENT
UID:9013
DTSTAMP:20251024T220000Z
SUMMARY:Oblastn&iacute; žebř&iacute;ček - Han&aacute;ck&aacute; oblast
DESCRIPTION:7. z&aacute;vod Han&aacute;ck&eacute;ho žebř&iacute;čku | Sportpark Boskovice Organi
 z&aacute;tor: KON - Region: HA -
CLASS:PUBLIC
DTSTART;TZID=Europe/Prague:20251025T110000
DTEND;TZID=Europe/Prague:20251025T150000
GEO:49.494779114491;16.680807362423
END:VEVENT
END:VCALENDAR</code>
 </pre>
    </span>
<span id="execution-results-GETapi-feed-kalendar-zavody-all" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-feed-kalendar-zavody-all"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-feed-kalendar-zavody-all"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-feed-kalendar-zavody-all" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-feed-kalendar-zavody-all">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-feed-kalendar-zavody-all" data-method="GET"
      data-path="api/feed/kalendar/zavody/all"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-feed-kalendar-zavody-all', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-feed-kalendar-zavody-all"
                    onclick="tryItOut('GETapi-feed-kalendar-zavody-all');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-feed-kalendar-zavody-all"
                    onclick="cancelTryOut('GETapi-feed-kalendar-zavody-all');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-feed-kalendar-zavody-all"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/feed/kalendar/zavody/all</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-feed-kalendar-zavody-all"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-feed-kalendar-zavody-all"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="calendar-GETapi-feed-kalendar-treninky-all">GET api/feed/kalendar/treninky/all</h2>

<p>
</p>



<span id="example-requests-GETapi-feed-kalendar-treninky-all">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/feed/kalendar/treninky/all';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/feed/kalendar/treninky/all" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/feed/kalendar/treninky/all"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-feed-kalendar-treninky-all">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: text/calendar; charset=utf-8
content-disposition: attachment; filename=&quot;abm-treninky.ics&quot;
x-ratelimit-limit: 30
x-ratelimit-remaining: 26
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">BEGIN:VCALENDAR
VERSION:2.0
PRODID:spatie/icalendar-generator
NAME:ABC - Kalend&aacute;ř tr&eacute;ninků
X-WR-CALNAME:ABC - Kalend&aacute;ř tr&eacute;ninků
DESCRIPTION:Kalendař tr&eacute;ninků na tento a n&aacute;sledujici rok.
X-WR-CALDESC:Kalendař tr&eacute;ninků na tento a n&aacute;sledujici rok.
END:VCALENDAR</code>
 </pre>
    </span>
<span id="execution-results-GETapi-feed-kalendar-treninky-all" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-feed-kalendar-treninky-all"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-feed-kalendar-treninky-all"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-feed-kalendar-treninky-all" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-feed-kalendar-treninky-all">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-feed-kalendar-treninky-all" data-method="GET"
      data-path="api/feed/kalendar/treninky/all"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-feed-kalendar-treninky-all', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-feed-kalendar-treninky-all"
                    onclick="tryItOut('GETapi-feed-kalendar-treninky-all');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-feed-kalendar-treninky-all"
                    onclick="cancelTryOut('GETapi-feed-kalendar-treninky-all');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-feed-kalendar-treninky-all"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/feed/kalendar/treninky/all</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-feed-kalendar-treninky-all"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-feed-kalendar-treninky-all"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="endpoints">Endpoints</h1>

    

                                <h2 id="endpoints-GETapi-user">GET api/user</h2>

<p>
</p>



<span id="example-requests-GETapi-user">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/user';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/user" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/user"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-user">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-user" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-user"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-user"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-user" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-user">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-user" data-method="GET"
      data-path="api/user"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-user', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-user"
                    onclick="tryItOut('GETapi-user');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-user"
                    onclick="cancelTryOut('GETapi-user');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-user"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/user</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="v1">V1</h1>

    <p>APIs V1</p>

                        <h2 id="v1-post">POST</h2>
                                        <p>
                    <p>News</p>
                </p>
                                        <h2 id="v1-GETapi-v1-posts">GET api/v1/posts</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-posts">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/v1/posts';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'from' =&gt; '2024-12-31',
            'to' =&gt; '2024-12-31',
            'page' =&gt; '1',
            'per_page' =&gt; 'architecto',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/posts?from=2024-12-31&amp;to=2024-12-31&amp;page=1&amp;per_page=architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/posts"
);

const params = {
    "from": "2024-12-31",
    "to": "2024-12-31",
    "page": "1",
    "per_page": "architecto",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-posts">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;user_id&quot;: 1,
            &quot;title&quot;: &quot;Novinka jak noha&quot;,
            &quot;editorial&quot;: null,
            &quot;img_url&quot;: null,
            &quot;content&quot;: &quot;Toto je prvn9 novinka&quot;,
            &quot;content_mode&quot;: 2,
            &quot;private&quot;: 1,
            &quot;created_at&quot;: &quot;2025-10-05T21:26:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-10-05T21:26:52.000000Z&quot;
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;http://localhost/api/v1/posts?page=1&quot;,
        &quot;last&quot;: null,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;current_page_url&quot;: &quot;http://localhost/api/v1/posts?page=1&quot;,
        &quot;from&quot;: 1,
        &quot;path&quot;: &quot;http://localhost/api/v1/posts&quot;,
        &quot;per_page&quot;: 20,
        &quot;to&quot;: 1
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-posts" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-posts"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-posts"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-posts" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-posts">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-posts" data-method="GET"
      data-path="api/v1/posts"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-posts', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-posts"
                    onclick="tryItOut('GETapi-v1-posts');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-posts"
                    onclick="cancelTryOut('GETapi-v1-posts');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-posts"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/posts</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-posts"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-posts"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>from</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="from"                data-endpoint="GETapi-v1-posts"
               value="2024-12-31"
               data-component="query">
    <br>
<p>Date from in Y-M-D Example: <code>2024-12-31</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="to"                data-endpoint="GETapi-v1-posts"
               value="2024-12-31"
               data-component="query">
    <br>
<p>Date to in Y-M-D Example: <code>2024-12-31</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-v1-posts"
               value="1"
               data-component="query">
    <br>
<p>Filter by whether a post is public or not. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="per_page"                data-endpoint="GETapi-v1-posts"
               value="architecto"
               data-component="query">
    <br>
<p>Field to sort by. Defaults to 'id'. Example: <code>architecto</code></p>
            </div>
                </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="php">php</button>
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
