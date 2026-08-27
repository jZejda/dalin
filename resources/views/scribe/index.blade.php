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
        var tryItOutBaseUrl = "https://demo.dalin.cz";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.11.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.11.0.js") }}"></script>

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
                    <ul id="tocify-header-sportevent" class="tocify-header">
                <li class="tocify-item level-1" data-unique="sportevent">
                    <a href="#sportevent">SportEvent</a>
                </li>
                                    <ul id="tocify-subheader-sportevent" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="sportevent-GETapi-v1-sport-event">
                                <a href="#sportevent-GETapi-v1-sport-event">Seznam závodů a akcí</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="sportevent-GETapi-v1-sport-event--sportEvent_id-">
                                <a href="#sportevent-GETapi-v1-sport-event--sportEvent_id-">Detail závodu</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-user" class="tocify-header">
                <li class="tocify-item level-1" data-unique="user">
                    <a href="#user">User</a>
                </li>
                                    <ul id="tocify-subheader-user" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="user-GETapi-v1-user-race-profiles">
                                <a href="#user-GETapi-v1-user-race-profiles">Seznam závodních profilů</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="user-GETapi-v1-user-entry">
                                <a href="#user-GETapi-v1-user-entry">Seznam přihlášek uživatele</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="user-POSTapi-v1-user-entry">
                                <a href="#user-POSTapi-v1-user-entry">Vytvoření přihlášky na závod</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="user-DELETEapi-v1-user-entry--userEntry_id-">
                                <a href="#user-DELETEapi-v1-user-entry--userEntry_id-">Zrušení přihlášky</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="user-GETapi-v1-user-credit-balance">
                                <a href="#user-GETapi-v1-user-credit-balance">Zůstatek konta uživatele</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-post" class="tocify-header">
                <li class="tocify-item level-1" data-unique="post">
                    <a href="#post">Post</a>
                </li>
                                    <ul id="tocify-subheader-post" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="post-GETapi-v1-post">
                                <a href="#post-GETapi-v1-post">Seznam příspěvků</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="post-GETapi-v1-post--post_id-">
                                <a href="#post-GETapi-v1-post--post_id-">Detail příspěvku</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="post-POSTapi-v1-post">
                                <a href="#post-POSTapi-v1-post">Vytvoření příspěvku</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="post-PUTapi-v1-post--post_id-">
                                <a href="#post-PUTapi-v1-post--post_id-">Úprava příspěvku</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-page" class="tocify-header">
                <li class="tocify-item level-1" data-unique="page">
                    <a href="#page">Page</a>
                </li>
                                    <ul id="tocify-subheader-page" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="page-GETapi-v1-page">
                                <a href="#page-GETapi-v1-page">Seznam stránek</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="page-GETapi-v1-page--page_id-">
                                <a href="#page-GETapi-v1-page--page_id-">Detail stránky</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: August 27, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>DaLin je klubový informační systém pro správu oddílů orientačních sportů — závody a přihlášky s napojením na ORIS, členské finance, novinky a obsah klubu. REST API v1 zpřístupňuje seznam závodů a jejich detail, závodní profily člena, vytváření a rušení přihlášek, zůstatek členského konta a klubové příspěvky a stránky. Všechny endpointy vyžadují platný API klíč v hlavičce <code>x-apikey</code> — klíč si každý člen vygeneruje v aplikaci na stránce <strong>Uživatelská nastavení</strong>.</p>
<aside>
    <strong>Base URL</strong>: <code>https://demo.dalin.cz</code>
</aside>
<pre><code>Tato dokumentace popisuje vše potřebné pro práci s DaLin API.

&lt;aside&gt;Při procházení uvidíš vpravo v tmavém panelu ukázky volání API v různých programovacích jazycích (na mobilu jako součást obsahu).
Jazyk ukázek přepneš záložkami vpravo nahoře (na mobilu v menu vlevo nahoře).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>To authenticate requests, include a <strong><code>x-apikey</code></strong> header with the value <strong><code>"{YOUR_API_KEY}"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>API klíč si vygeneruješ v aplikaci na stránce <b>Uživatelská nastavení</b>. Klíč posílej v hlavičce <code>x-apikey</code> každého požadavku.</p>

        <h1 id="sportevent">SportEvent</h1>

    <p>Závody a akce klubu — výpis, detail a kategorie tříd.</p>

                                <h2 id="sportevent-GETapi-v1-sport-event">Seznam závodů a akcí</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Vrátí stránkovaný seznam závodů a akcí klubu s možností filtrování.</p>

<span id="example-requests-GETapi-v1-sport-event">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/sport-event';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'from' =&gt; '2026-01-01',
            'to' =&gt; '2026-12-31',
            'event_type' =&gt; 'race',
            'class_definition_id' =&gt; '15',
            'page' =&gt; '1',
            'per_page' =&gt; '20',
        ],
        'json' =&gt; [
            'from' =&gt; '2026-08-27',
            'to' =&gt; '2026-08-27',
            'class_definition_id' =&gt; 16,
            'per_page' =&gt; 22,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://demo.dalin.cz/api/v1/sport-event?from=2026-01-01&amp;to=2026-12-31&amp;event_type=race&amp;class_definition_id=15&amp;page=1&amp;per_page=20" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"from\": \"2026-08-27\",
    \"to\": \"2026-08-27\",
    \"class_definition_id\": 16,
    \"per_page\": 22
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/sport-event"
);

const params = {
    "from": "2026-01-01",
    "to": "2026-12-31",
    "event_type": "race",
    "class_definition_id": "15",
    "page": "1",
    "per_page": "20",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "from": "2026-08-27",
    "to": "2026-08-27",
    "class_definition_id": 16,
    "per_page": 22
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-sport-event">
            <blockquote>
            <p>Example response (200, Example Sport Event List):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1201,
            &quot;name&quot;: &quot;Mistrovstvi oblasti na kratke trati&quot;,
            &quot;date&quot;: &quot;2026-05-16&quot;,
            &quot;entry_date&quot;: &quot;2026-05-08 23:59:59&quot;,
            &quot;event_type&quot;: {
                &quot;value&quot;: &quot;race&quot;,
                &quot;label&quot;: &quot;Zavod&quot;
            },
            &quot;oris_id&quot;: 8703,
            &quot;use_oris_for_entries&quot;: true,
            &quot;cancelled&quot;: false,
            &quot;categories&quot;: [
                {
                    &quot;id&quot;: 15,
                    &quot;name&quot;: &quot;H21&quot;
                },
                {
                    &quot;id&quot;: 16,
                    &quot;name&quot;: &quot;D21&quot;
                }
            ],
            &quot;created_at&quot;: &quot;2026-03-01T10:20:11.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-05T15:42:00.000000Z&quot;
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;http://localhost/api/v1/sport-event/list?page=1&quot;,
        &quot;last&quot;: null,
        &quot;prev&quot;: null,
        &quot;next&quot;: &quot;http://localhost/api/v1/sport-event/list?page=2&quot;
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;current_page_url&quot;: &quot;http://localhost/api/v1/sport-event/list?page=1&quot;,
        &quot;from&quot;: 1,
        &quot;path&quot;: &quot;http://localhost/api/v1/sport-event/list&quot;,
        &quot;per_page&quot;: 20,
        &quot;to&quot;: 1
    },
    &quot;components&quot;: {
        &quot;event_type_options&quot;: {
            &quot;race&quot;: &quot;Zavod&quot;,
            &quot;training&quot;: &quot;Trenink&quot;,
            &quot;trainingCamp&quot;: &quot;Soustredeni&quot;,
            &quot;other&quot;: &quot;Ostatni&quot;
        },
        &quot;category_component_description&quot;: &quot;categories contains class definitions assigned to each event (SportClass -&gt; SportClassDefinition).&quot;,
        &quot;event_type_component_description&quot;: &quot;event_type contains both machine value and translated label.&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-sport-event" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-sport-event"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-sport-event"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-sport-event" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-sport-event">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-sport-event" data-method="GET"
      data-path="api/v1/sport-event"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-sport-event', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-sport-event"
                    onclick="tryItOut('GETapi-v1-sport-event');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-sport-event"
                    onclick="cancelTryOut('GETapi-v1-sport-event');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-sport-event"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/sport-event</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="GETapi-v1-sport-event"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-sport-event"
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
                              name="Accept"                data-endpoint="GETapi-v1-sport-event"
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
                              name="from"                data-endpoint="GETapi-v1-sport-event"
               value="2026-01-01"
               data-component="query">
    <br>
<p>Date from in Y-m-d format. Example: <code>2026-01-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="to"                data-endpoint="GETapi-v1-sport-event"
               value="2026-12-31"
               data-component="query">
    <br>
<p>Date to in Y-m-d format. Example: <code>2026-12-31</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>event_type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="event_type"                data-endpoint="GETapi-v1-sport-event"
               value="race"
               data-component="query">
    <br>
<p>Filter by event type category. Allowed values: race, training, trainingCamp, other. Example: <code>race</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>class_definition_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="class_definition_id"                data-endpoint="GETapi-v1-sport-event"
               value="15"
               data-component="query">
    <br>
<p>Filter events by category/class definition ID assigned to the event. Example: <code>15</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-v1-sport-event"
               value="1"
               data-component="query">
    <br>
<p>Page number for pagination. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-sport-event"
               value="20"
               data-component="query">
    <br>
<p>Number of items per page. Defaults to 20. Example: <code>20</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>from</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="from"                data-endpoint="GETapi-v1-sport-event"
               value="2026-08-27"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>Y-m-d</code>. Example: <code>2026-08-27</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="to"                data-endpoint="GETapi-v1-sport-event"
               value="2026-08-27"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>Y-m-d</code>. Example: <code>2026-08-27</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>event_type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="event_type"                data-endpoint="GETapi-v1-sport-event"
               value=""
               data-component="body">
    <br>

        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>class_definition_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="class_definition_id"                data-endpoint="GETapi-v1-sport-event"
               value="16"
               data-component="body">
    <br>
<p>Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-sport-event"
               value="22"
               data-component="body">
    <br>
<p>:Attribute musí být větší než 1. :Attribute nemůže být větší než 100. Example: <code>22</code></p>
        </div>
        </form>

                    <h2 id="sportevent-GETapi-v1-sport-event--sportEvent_id-">Detail závodu</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Vrátí detail závodu včetně kategorií, štafet a možností etap pro přihlášení.</p>

<span id="example-requests-GETapi-v1-sport-event--sportEvent_id-">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/sport-event/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://demo.dalin.cz/api/v1/sport-event/1" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/sport-event/1"
);

const headers = {
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-sport-event--sportEvent_id-">
            <blockquote>
            <p>Example response (200, Example Sport Event Detail):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1201,
        &quot;name&quot;: &quot;Mistrovstvi oblasti na kratke trati&quot;,
        &quot;alt_name&quot;: null,
        &quot;oris_id&quot;: 8703,
        &quot;use_oris_for_entries&quot;: true,
        &quot;date&quot;: &quot;2026-05-16&quot;,
        &quot;date_end&quot;: null,
        &quot;place&quot;: &quot;Lipnice nad Sazavou&quot;,
        &quot;organization&quot;: [
            &quot;ABC&quot;
        ],
        &quot;region&quot;: [
            &quot;VC&quot;
        ],
        &quot;entry_desc&quot;: null,
        &quot;event_info&quot;: &quot;Centrum zavodu na louce u obce.&quot;,
        &quot;event_warning&quot;: null,
        &quot;event_type&quot;: {
            &quot;value&quot;: &quot;race&quot;,
            &quot;label&quot;: &quot;Zavod&quot;
        },
        &quot;discipline&quot;: {
            &quot;id&quot;: 2,
            &quot;short_name&quot;: &quot;KT&quot;,
            &quot;long_name&quot;: &quot;Kratka trat&quot;
        },
        &quot;level&quot;: {
            &quot;id&quot;: 5,
            &quot;short_name&quot;: &quot;OF&quot;,
            &quot;long_name&quot;: &quot;Oblastni zebricek&quot;
        },
        &quot;is_relay&quot;: false,
        &quot;cancelled&quot;: false,
        &quot;cancelled_reason&quot;: null,
        &quot;ranking&quot;: true,
        &quot;ranking_coefficient&quot;: 1.02,
        &quot;entry_dates&quot;: {
            &quot;entry_date_1&quot;: &quot;2026-05-08 23:59:59&quot;,
            &quot;entry_date_2&quot;: &quot;2026-05-12 23:59:59&quot;,
            &quot;entry_date_3&quot;: null,
            &quot;last_entry_date&quot;: &quot;2026-05-12 23:59:59&quot;
        },
        &quot;entry_deadline_passed&quot;: false,
        &quot;start_time&quot;: &quot;10:00&quot;,
        &quot;gps&quot;: {
            &quot;lat&quot;: &quot;49.6122&quot;,
            &quot;lon&quot;: &quot;15.4114&quot;
        },
        &quot;stages&quot;: null,
        &quot;stage_options&quot;: [],
        &quot;classes&quot;: [
            {
                &quot;id&quot;: 351,
                &quot;oris_id&quot;: 123456,
                &quot;name&quot;: &quot;H21&quot;,
                &quot;distance&quot;: &quot;5.4&quot;,
                &quot;climbing&quot;: &quot;120&quot;,
                &quot;controls&quot;: &quot;18&quot;,
                &quot;fee&quot;: 150,
                &quot;legs&quot;: null,
                &quot;class_definition&quot;: {
                    &quot;id&quot;: 15,
                    &quot;name&quot;: &quot;H21&quot;,
                    &quot;gender&quot;: &quot;M&quot;,
                    &quot;age_from&quot;: 21,
                    &quot;age_to&quot;: 34
                }
            },
            {
                &quot;id&quot;: 352,
                &quot;oris_id&quot;: 123457,
                &quot;name&quot;: &quot;D21&quot;,
                &quot;distance&quot;: &quot;4.6&quot;,
                &quot;climbing&quot;: &quot;95&quot;,
                &quot;controls&quot;: &quot;16&quot;,
                &quot;fee&quot;: 150,
                &quot;legs&quot;: null,
                &quot;class_definition&quot;: {
                    &quot;id&quot;: 16,
                    &quot;name&quot;: &quot;D21&quot;,
                    &quot;gender&quot;: &quot;F&quot;,
                    &quot;age_from&quot;: 21,
                    &quot;age_to&quot;: 34
                }
            }
        ],
        &quot;services&quot;: [
            {
                &quot;id&quot;: 12,
                &quot;name&quot;: &quot;Ubytovani - telocvicna&quot;,
                &quot;unit_price&quot;: 100,
                &quot;qty_available&quot;: 40,
                &quot;qty_remaining&quot;: 25,
                &quot;last_booking_date_time&quot;: &quot;2026-05-08 23:59:59&quot;
            }
        ],
        &quot;links&quot;: [
            {
                &quot;id&quot;: 77,
                &quot;name&quot;: &quot;Rozpis zavodu&quot;,
                &quot;url&quot;: &quot;https://oris.orientacnisporty.cz/Zavod?id=8703&quot;,
                &quot;type&quot;: &quot;oris&quot;
            }
        ],
        &quot;relay_teams&quot;: [],
        &quot;created_at&quot;: &quot;2026-03-01T10:20:11.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-05T15:42:00.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Sport event not found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Not found.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-sport-event--sportEvent_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-sport-event--sportEvent_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-sport-event--sportEvent_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-sport-event--sportEvent_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-sport-event--sportEvent_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-sport-event--sportEvent_id-" data-method="GET"
      data-path="api/v1/sport-event/{sportEvent_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-sport-event--sportEvent_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-sport-event--sportEvent_id-"
                    onclick="tryItOut('GETapi-v1-sport-event--sportEvent_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-sport-event--sportEvent_id-"
                    onclick="cancelTryOut('GETapi-v1-sport-event--sportEvent_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-sport-event--sportEvent_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/sport-event/{sportEvent_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="GETapi-v1-sport-event--sportEvent_id-"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-sport-event--sportEvent_id-"
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
                              name="Accept"                data-endpoint="GETapi-v1-sport-event--sportEvent_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sportEvent_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="sportEvent_id"                data-endpoint="GETapi-v1-sport-event--sportEvent_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the sportEvent. Example: <code>1</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sportEvent</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="sportEvent"                data-endpoint="GETapi-v1-sport-event--sportEvent_id-"
               value="1201"
               data-component="url">
    <br>
<p>Sport event ID. Example: <code>1201</code></p>
            </div>
                    </form>

                <h1 id="user">User</h1>

    <p>Přihlášený uživatel — profil, závodní profily, přihlášky a zůstatek kreditu.</p>

                                <h2 id="user-GETapi-v1-user-race-profiles">Seznam závodních profilů</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Vrátí závodní profily přihlášeného uživatele (výchozí jen aktivní).</p>

<span id="example-requests-GETapi-v1-user-race-profiles">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/user/race-profiles';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'all' =&gt; '0',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://demo.dalin.cz/api/v1/user/race-profiles?all=" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/user/race-profiles"
);

const params = {
    "all": "0",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-user-race-profiles">
            <blockquote>
            <p>Example response (200, Example User Race Profiles):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 101,
            &quot;reg_number&quot;: &quot;A12-0001&quot;,
            &quot;first_name&quot;: &quot;Jan&quot;,
            &quot;last_name&quot;: &quot;Novak&quot;,
            &quot;full_name&quot;: &quot;A12-0001 - Jan Novak&quot;,
            &quot;email&quot;: &quot;jan.novak@example.com&quot;,
            &quot;phone&quot;: &quot;+420777111222&quot;,
            &quot;gender&quot;: &quot;M&quot;,
            &quot;active&quot;: true,
            &quot;active_until&quot;: &quot;2026-12-31&quot;,
            &quot;created_at&quot;: &quot;2025-01-15T10:22:00.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-02-01T08:10:44.000000Z&quot;
        },
        {
            &quot;id&quot;: 102,
            &quot;reg_number&quot;: &quot;A12-0002&quot;,
            &quot;first_name&quot;: &quot;Petra&quot;,
            &quot;last_name&quot;: &quot;Novakova&quot;,
            &quot;full_name&quot;: &quot;A12-0002 - Petra Novakova&quot;,
            &quot;email&quot;: null,
            &quot;phone&quot;: null,
            &quot;gender&quot;: &quot;F&quot;,
            &quot;active&quot;: false,
            &quot;active_until&quot;: &quot;2024-12-31&quot;,
            &quot;created_at&quot;: &quot;2024-03-10T09:00:00.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-01-03T11:18:19.000000Z&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-user-race-profiles" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-user-race-profiles"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-user-race-profiles"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-user-race-profiles" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-user-race-profiles">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-user-race-profiles" data-method="GET"
      data-path="api/v1/user/race-profiles"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-user-race-profiles', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-user-race-profiles"
                    onclick="tryItOut('GETapi-v1-user-race-profiles');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-user-race-profiles"
                    onclick="cancelTryOut('GETapi-v1-user-race-profiles');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-user-race-profiles"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/user/race-profiles</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="GETapi-v1-user-race-profiles"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-user-race-profiles"
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
                              name="Accept"                data-endpoint="GETapi-v1-user-race-profiles"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>all</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <label data-endpoint="GETapi-v1-user-race-profiles" style="display: none">
            <input type="radio" name="all"
                   value="1"
                   data-endpoint="GETapi-v1-user-race-profiles"
                   data-component="query"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETapi-v1-user-race-profiles" style="display: none">
            <input type="radio" name="all"
                   value="0"
                   data-endpoint="GETapi-v1-user-race-profiles"
                   data-component="query"             >
            <code>false</code>
        </label>
    <br>
<p>When true, includes both active and inactive race profiles. Defaults to false (active only). Example: <code>false</code></p>
            </div>
                </form>

                    <h2 id="user-GETapi-v1-user-entry">Seznam přihlášek uživatele</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Vrátí přihlášky všech závodních profilů uživatele, výchozí od dneška dál.</p>

<span id="example-requests-GETapi-v1-user-entry">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/user/entry';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'from' =&gt; '2026-01-01',
            'to' =&gt; '2026-12-31',
            'page' =&gt; '1',
            'per_page' =&gt; '20',
        ],
        'json' =&gt; [
            'from' =&gt; '2026-08-27',
            'to' =&gt; '2026-08-27',
            'per_page' =&gt; 1,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://demo.dalin.cz/api/v1/user/entry?from=2026-01-01&amp;to=2026-12-31&amp;page=1&amp;per_page=20" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"from\": \"2026-08-27\",
    \"to\": \"2026-08-27\",
    \"per_page\": 1
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/user/entry"
);

const params = {
    "from": "2026-01-01",
    "to": "2026-12-31",
    "page": "1",
    "per_page": "20",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "from": "2026-08-27",
    "to": "2026-08-27",
    "per_page": 1
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-user-entry">
            <blockquote>
            <p>Example response (200, Example User Entry List):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 3451,
            &quot;sport_event&quot;: {
                &quot;id&quot;: 1201,
                &quot;name&quot;: &quot;Mistrovstvi oblasti na kratke trati&quot;,
                &quot;date&quot;: &quot;2026-05-16&quot;
            },
            &quot;race_profile&quot;: {
                &quot;id&quot;: 101,
                &quot;name&quot;: &quot;A12-0001 - Jan Novak&quot;
            },
            &quot;class_name&quot;: &quot;H21&quot;,
            &quot;requested_start&quot;: &quot;10:30&quot;,
            &quot;rent_si&quot;: false,
            &quot;entry_stages&quot;: [
                &quot;stage1&quot;
            ],
            &quot;entry_status&quot;: &quot;created&quot;,
            &quot;entry_created&quot;: &quot;2026-05-01 20:15:00&quot;,
            &quot;created_at&quot;: &quot;2026-05-01T20:15:00.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-05-01T20:15:00.000000Z&quot;
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;http://localhost/api/user/entry?page=1&quot;,
        &quot;last&quot;: null,
        &quot;prev&quot;: null,
        &quot;next&quot;: &quot;http://localhost/api/user/entry?page=2&quot;
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;current_page_url&quot;: &quot;http://localhost/api/user/entry?page=1&quot;,
        &quot;from&quot;: 1,
        &quot;path&quot;: &quot;http://localhost/api/user/entry&quot;,
        &quot;per_page&quot;: 20,
        &quot;to&quot;: 1
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-user-entry" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-user-entry"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-user-entry"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-user-entry" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-user-entry">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-user-entry" data-method="GET"
      data-path="api/v1/user/entry"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-user-entry', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-user-entry"
                    onclick="tryItOut('GETapi-v1-user-entry');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-user-entry"
                    onclick="cancelTryOut('GETapi-v1-user-entry');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-user-entry"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/user/entry</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="GETapi-v1-user-entry"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-user-entry"
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
                              name="Accept"                data-endpoint="GETapi-v1-user-entry"
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
                              name="from"                data-endpoint="GETapi-v1-user-entry"
               value="2026-01-01"
               data-component="query">
    <br>
<p>Date from in Y-m-d format. Filters by sport event date. Example: <code>2026-01-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="to"                data-endpoint="GETapi-v1-user-entry"
               value="2026-12-31"
               data-component="query">
    <br>
<p>Date to in Y-m-d format. Filters by sport event date. Example: <code>2026-12-31</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-v1-user-entry"
               value="1"
               data-component="query">
    <br>
<p>Page number for pagination. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-user-entry"
               value="20"
               data-component="query">
    <br>
<p>Number of items per page. Defaults to 20. Example: <code>20</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>from</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="from"                data-endpoint="GETapi-v1-user-entry"
               value="2026-08-27"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>Y-m-d</code>. Example: <code>2026-08-27</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="to"                data-endpoint="GETapi-v1-user-entry"
               value="2026-08-27"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>Y-m-d</code>. Example: <code>2026-08-27</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-user-entry"
               value="1"
               data-component="body">
    <br>
<p>:Attribute musí být větší než 1. :Attribute nemůže být větší než 100. Example: <code>1</code></p>
        </div>
        </form>

                    <h2 id="user-POSTapi-v1-user-entry">Vytvoření přihlášky na závod</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Přihlásí závodní profil uživatele na závod — včetně štafet, etap a ORIS závodů.</p>

<span id="example-requests-POSTapi-v1-user-entry">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/user/entry';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'sport_event_id' =&gt; 1201,
            'race_profile_id' =&gt; 12,
            'class_id' =&gt; 351,
            'relay_team_member_id' =&gt; 16,
            'si' =&gt; 8123456,
            'rent_si' =&gt; false,
            'note' =&gt; 'Note for organizer',
            'club_note' =&gt; 'architecto',
            'requested_start' =&gt; 'architecto',
            'entry_stages' =&gt; ['architecto'],
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://demo.dalin.cz/api/v1/user/entry" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"sport_event_id\": 1201,
    \"race_profile_id\": 12,
    \"class_id\": 351,
    \"relay_team_member_id\": 16,
    \"si\": 8123456,
    \"rent_si\": false,
    \"note\": \"Note for organizer\",
    \"club_note\": \"architecto\",
    \"requested_start\": \"architecto\",
    \"entry_stages\": [
        \"architecto\"
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/user/entry"
);

const headers = {
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "sport_event_id": 1201,
    "race_profile_id": 12,
    "class_id": 351,
    "relay_team_member_id": 16,
    "si": 8123456,
    "rent_si": false,
    "note": "Note for organizer",
    "club_note": "architecto",
    "requested_start": "architecto",
    "entry_stages": [
        "architecto"
    ]
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-user-entry">
            <blockquote>
            <p>Example response (201, Entry created):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 5301,
        &quot;sport_event&quot;: {
            &quot;id&quot;: 1201,
            &quot;name&quot;: &quot;Mistrovstvi oblasti na kratke trati&quot;,
            &quot;date&quot;: &quot;2026-05-16&quot;
        },
        &quot;race_profile&quot;: {
            &quot;id&quot;: 12,
            &quot;name&quot;: &quot;ABC1234 - Jan Novak&quot;
        },
        &quot;class_name&quot;: &quot;H21&quot;,
        &quot;requested_start&quot;: null,
        &quot;rent_si&quot;: false,
        &quot;entry_stages&quot;: null,
        &quot;entry_status&quot;: &quot;create&quot;,
        &quot;oris_entry_id&quot;: 987654,
        &quot;entry_created&quot;: &quot;2026-05-02 18:21:05&quot;,
        &quot;created_at&quot;: &quot;2026-05-02T18:21:05.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-05-02T18:21:05.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (403, Foreign race profile):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Race profile does not belong to the authenticated user.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (409, Duplicate entry):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Race profile already has an entry for this event.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Business rule rejected the entry):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Entry deadline has passed.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-user-entry" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-user-entry"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-user-entry"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-user-entry" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-user-entry">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-user-entry" data-method="POST"
      data-path="api/v1/user/entry"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-user-entry', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-user-entry"
                    onclick="tryItOut('POSTapi-v1-user-entry');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-user-entry"
                    onclick="cancelTryOut('POSTapi-v1-user-entry');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-user-entry"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/user/entry</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="POSTapi-v1-user-entry"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-user-entry"
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
                              name="Accept"                data-endpoint="POSTapi-v1-user-entry"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sport_event_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="sport_event_id"                data-endpoint="POSTapi-v1-user-entry"
               value="1201"
               data-component="body">
    <br>
<p>ID of the sport event (see GET /api/v1/sport-event). Example: <code>1201</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>race_profile_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="race_profile_id"                data-endpoint="POSTapi-v1-user-entry"
               value="12"
               data-component="body">
    <br>
<p>ID of one of the authenticated user's race profiles (see GET /api/v1/user/race-profiles). Example: <code>12</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>class_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="class_id"                data-endpoint="POSTapi-v1-user-entry"
               value="351"
               data-component="body">
    <br>
<p>ID of the event class (see classes[].id in GET /api/v1/sport-event/{id}). Required for non-relay events. Example: <code>351</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>relay_team_member_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="relay_team_member_id"                data-endpoint="POSTapi-v1-user-entry"
               value="16"
               data-component="body">
    <br>
<p>Free relay slot ID (see relay_teams[].members[].relay_team_member_id in the event detail). Required for relay events instead of class_id. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>si</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="si"                data-endpoint="POSTapi-v1-user-entry"
               value="8123456"
               data-component="body">
    <br>
<p>SI chip number. Defaults to the SI stored on the race profile. Example: <code>8123456</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>rent_si</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <label data-endpoint="POSTapi-v1-user-entry" style="display: none">
            <input type="radio" name="rent_si"
                   value="true"
                   data-endpoint="POSTapi-v1-user-entry"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTapi-v1-user-entry" style="display: none">
            <input type="radio" name="rent_si"
                   value="false"
                   data-endpoint="POSTapi-v1-user-entry"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Request an SI chip rental. Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>note</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="note"                data-endpoint="POSTapi-v1-user-entry"
               value="Note for organizer"
               data-component="body">
    <br>
<p>Note for the event organizer. Example: <code>Note for organizer</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>club_note</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="club_note"                data-endpoint="POSTapi-v1-user-entry"
               value="architecto"
               data-component="body">
    <br>
<p>Internal club note. Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>requested_start</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="requested_start"                data-endpoint="POSTapi-v1-user-entry"
               value="architecto"
               data-component="body">
    <br>
<p>Requested start time. Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>entry_stages</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="entry_stages[0]"                data-endpoint="POSTapi-v1-user-entry"
               data-component="body">
        <input type="text" style="display: none"
               name="entry_stages[1]"                data-endpoint="POSTapi-v1-user-entry"
               data-component="body">
    <br>
<p>Stages to enter for multi-stage events, e.g. ["stage1", "stage2"] (see stage_options in the event detail).</p>
        </div>
        </form>

                    <h2 id="user-DELETEapi-v1-user-entry--userEntry_id-">Zrušení přihlášky</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Zruší přihlášku uživatele, u ORIS závodů včetně odhlášení v ORISu.</p>

<span id="example-requests-DELETEapi-v1-user-entry--userEntry_id-">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/user/entry/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "https://demo.dalin.cz/api/v1/user/entry/1" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/user/entry/1"
);

const headers = {
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-user-entry--userEntry_id-">
            <blockquote>
            <p>Example response (200, Entry cancelled):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 5301,
        &quot;entry_status&quot;: &quot;cancel&quot;,
        &quot;was_oris_entry&quot;: true
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Deadline passed, entry cannot be cancelled):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Entry deadline has passed.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Already cancelled):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Entry is already cancelled.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-user-entry--userEntry_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-user-entry--userEntry_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-user-entry--userEntry_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-user-entry--userEntry_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-user-entry--userEntry_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-user-entry--userEntry_id-" data-method="DELETE"
      data-path="api/v1/user/entry/{userEntry_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-user-entry--userEntry_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-user-entry--userEntry_id-"
                    onclick="tryItOut('DELETEapi-v1-user-entry--userEntry_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-user-entry--userEntry_id-"
                    onclick="cancelTryOut('DELETEapi-v1-user-entry--userEntry_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-user-entry--userEntry_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/user/entry/{userEntry_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="DELETEapi-v1-user-entry--userEntry_id-"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-user-entry--userEntry_id-"
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
                              name="Accept"                data-endpoint="DELETEapi-v1-user-entry--userEntry_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>userEntry_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="userEntry_id"                data-endpoint="DELETEapi-v1-user-entry--userEntry_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the userEntry. Example: <code>1</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>userEntry</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="userEntry"                data-endpoint="DELETEapi-v1-user-entry--userEntry_id-"
               value="5301"
               data-component="url">
    <br>
<p>User entry ID (see GET /api/v1/user/entry). Example: <code>5301</code></p>
            </div>
                    </form>

                    <h2 id="user-GETapi-v1-user-credit-balance">Zůstatek konta uživatele</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Vrátí aktuální zůstatek kreditu přihlášeného uživatele.</p>

<span id="example-requests-GETapi-v1-user-credit-balance">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/user/credit-balance';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://demo.dalin.cz/api/v1/user/credit-balance" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/user/credit-balance"
);

const headers = {
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-user-credit-balance">
            <blockquote>
            <p>Example response (200, Example User Credit Balance):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;amount&quot;: 1250.5,
        &quot;currency&quot;: &quot;CZK&quot;,
        &quot;user_id&quot;: 15,
        &quot;updated_at&quot;: &quot;2026-04-15 11:10:00&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-user-credit-balance" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-user-credit-balance"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-user-credit-balance"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-user-credit-balance" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-user-credit-balance">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-user-credit-balance" data-method="GET"
      data-path="api/v1/user/credit-balance"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-user-credit-balance', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-user-credit-balance"
                    onclick="tryItOut('GETapi-v1-user-credit-balance');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-user-credit-balance"
                    onclick="cancelTryOut('GETapi-v1-user-credit-balance');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-user-credit-balance"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/user/credit-balance</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="GETapi-v1-user-credit-balance"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-user-credit-balance"
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
                              name="Accept"                data-endpoint="GETapi-v1-user-credit-balance"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="post">Post</h1>

    <p>Novinky a příspěvky klubu — veřejné i interní (jen pro členy).</p>

                                <h2 id="post-GETapi-v1-post">Seznam příspěvků</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Vrátí stránkovaný seznam novinek a příspěvků klubu.</p>

<span id="example-requests-GETapi-v1-post">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/post';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
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
    --get "https://demo.dalin.cz/api/v1/post?from=2024-12-31&amp;to=2024-12-31&amp;page=1&amp;per_page=architecto" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/post"
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
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-post">
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
        &quot;first&quot;: &quot;http://localhost/api/v1/posts/list?page=1&quot;,
        &quot;last&quot;: null,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;current_page_url&quot;: &quot;http://localhost/api/v1/posts/list?page=1&quot;,
        &quot;from&quot;: 1,
        &quot;path&quot;: &quot;http://localhost/api/v1/posts/list&quot;,
        &quot;per_page&quot;: 20,
        &quot;to&quot;: 1
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-post" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-post"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-post"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-post" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-post">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-post" data-method="GET"
      data-path="api/v1/post"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-post', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-post"
                    onclick="tryItOut('GETapi-v1-post');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-post"
                    onclick="cancelTryOut('GETapi-v1-post');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-post"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/post</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="GETapi-v1-post"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-post"
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
                              name="Accept"                data-endpoint="GETapi-v1-post"
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
                              name="from"                data-endpoint="GETapi-v1-post"
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
                              name="to"                data-endpoint="GETapi-v1-post"
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
               step="any"               name="page"                data-endpoint="GETapi-v1-post"
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
                              name="per_page"                data-endpoint="GETapi-v1-post"
               value="architecto"
               data-component="query">
    <br>
<p>Field to sort by. Defaults to 'id'. Example: <code>architecto</code></p>
            </div>
                </form>

                    <h2 id="post-GETapi-v1-post--post_id-">Detail příspěvku</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Vrátí detail konkrétního příspěvku podle ID.</p>

<span id="example-requests-GETapi-v1-post--post_id-">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/post/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://demo.dalin.cz/api/v1/post/1" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/post/1"
);

const headers = {
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-post--post_id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
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
}</code>
 </pre>
            <blockquote>
            <p>Example response (200, Example with HTML content (content_mode = 1)):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 2,
        &quot;user_id&quot;: 1,
        &quot;title&quot;: &quot;&lt;h1&gt;HTML Post&lt;/h1&gt;&quot;,
        &quot;editorial&quot;: null,
        &quot;img_url&quot;: null,
        &quot;content&quot;: &quot;&lt;p&gt;This is HTML content&lt;/p&gt;&quot;,
        &quot;content_mode&quot;: 1,
        &quot;private&quot;: 0,
        &quot;created_at&quot;: &quot;2025-10-05T21:26:52.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2025-10-05T21:26:52.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (200, Example with TipTap JSON content (content_mode = 3)):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 3,
        &quot;user_id&quot;: 1,
        &quot;title&quot;: &quot;TipTap Post&quot;,
        &quot;editorial&quot;: null,
        &quot;img_url&quot;: null,
        &quot;content&quot;: {
            &quot;type&quot;: &quot;doc&quot;,
            &quot;content&quot;: [
                {
                    &quot;type&quot;: &quot;paragraph&quot;,
                    &quot;content&quot;: [
                        {
                            &quot;type&quot;: &quot;text&quot;,
                            &quot;text&quot;: &quot;This is TipTap JSON content&quot;
                        }
                    ]
                }
            ]
        },
        &quot;content_mode&quot;: 3,
        &quot;private&quot;: 0,
        &quot;created_at&quot;: &quot;2025-10-05T21:26:52.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2025-10-05T21:26:52.000000Z&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-post--post_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-post--post_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-post--post_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-post--post_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-post--post_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-post--post_id-" data-method="GET"
      data-path="api/v1/post/{post_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-post--post_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-post--post_id-"
                    onclick="tryItOut('GETapi-v1-post--post_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-post--post_id-"
                    onclick="cancelTryOut('GETapi-v1-post--post_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-post--post_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/post/{post_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="GETapi-v1-post--post_id-"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-post--post_id-"
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
                              name="Accept"                data-endpoint="GETapi-v1-post--post_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>post_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="post_id"                data-endpoint="GETapi-v1-post--post_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the post. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="post-POSTapi-v1-post">Vytvoření příspěvku</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Vytvoří novou novinku — veřejnou nebo interní (viditelnou jen členům), podle příznaku <code>private</code>.
Autorem se stává uživatel, jehož API klíč byl použit k autentizaci.</p>

<span id="example-requests-POSTapi-v1-post">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/post';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'title' =&gt; 'Nová novinka z klubu',
            'content' =&gt; 'Obsah novinky v Markdownu.',
            'content_mode' =&gt; 2,
            'private' =&gt; true,
            'editorial' =&gt; 'architecto',
            'img_url' =&gt; 'http://www.bailey.biz/quos-velit-et-fugiat-sunt-nihil-accusantium-harum.html',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://demo.dalin.cz/api/v1/post" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"title\": \"Nová novinka z klubu\",
    \"content\": \"Obsah novinky v Markdownu.\",
    \"content_mode\": 2,
    \"private\": true,
    \"editorial\": \"architecto\",
    \"img_url\": \"http:\\/\\/www.bailey.biz\\/quos-velit-et-fugiat-sunt-nihil-accusantium-harum.html\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/post"
);

const headers = {
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "title": "Nová novinka z klubu",
    "content": "Obsah novinky v Markdownu.",
    "content_mode": 2,
    "private": true,
    "editorial": "architecto",
    "img_url": "http:\/\/www.bailey.biz\/quos-velit-et-fugiat-sunt-nihil-accusantium-harum.html"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-post">
            <blockquote>
            <p>Example response (201, Post created):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 4,
        &quot;user_id&quot;: 1,
        &quot;title&quot;: &quot;Nov&aacute; novinka z klubu&quot;,
        &quot;editorial&quot;: null,
        &quot;img_url&quot;: null,
        &quot;content&quot;: &quot;Obsah novinky v Markdownu.&quot;,
        &quot;content_mode&quot;: 2,
        &quot;private&quot;: 1,
        &quot;created_at&quot;: &quot;2026-08-27T08:00:00.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-08-27T08:00:00.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validation error):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;The title field is required.&quot;,
    &quot;errors&quot;: {
        &quot;title&quot;: [
            &quot;The title field is required.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-post" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-post"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-post"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-post" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-post">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-post" data-method="POST"
      data-path="api/v1/post"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-post', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-post"
                    onclick="tryItOut('POSTapi-v1-post');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-post"
                    onclick="cancelTryOut('POSTapi-v1-post');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-post"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/post</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="POSTapi-v1-post"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-post"
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
                              name="Accept"                data-endpoint="POSTapi-v1-post"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>title</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="title"                data-endpoint="POSTapi-v1-post"
               value="Nová novinka z klubu"
               data-component="body">
    <br>
<p>Post title. Example: <code>Nová novinka z klubu</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>content</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="content"                data-endpoint="POSTapi-v1-post"
               value="Obsah novinky v Markdownu."
               data-component="body">
    <br>
<p>Post content. Plain string for HTML/Markdown (content_mode 1/2), or a TipTap JSON document for content_mode 3. Example: <code>Obsah novinky v Markdownu.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>content_mode</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="content_mode"                data-endpoint="POSTapi-v1-post"
               value="2"
               data-component="body">
    <br>
<p>Content format: 1 = HTML, 2 = Markdown, 3 = TipTap JSON. Defaults to 2 (Markdown). Example: <code>2</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>private</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <label data-endpoint="POSTapi-v1-post" style="display: none">
            <input type="radio" name="private"
                   value="true"
                   data-endpoint="POSTapi-v1-post"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTapi-v1-post" style="display: none">
            <input type="radio" name="private"
                   value="false"
                   data-endpoint="POSTapi-v1-post"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>true = internal post (members only), false = public. Defaults to true. Example: <code>true</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>editorial</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="editorial"                data-endpoint="POSTapi-v1-post"
               value="architecto"
               data-component="body">
    <br>
<p>Optional editorial note. Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>img_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="img_url"                data-endpoint="POSTapi-v1-post"
               value="http://www.bailey.biz/quos-velit-et-fugiat-sunt-nihil-accusantium-harum.html"
               data-component="body">
    <br>
<p>Optional cover image URL/path. Example: <code>http://www.bailey.biz/quos-velit-et-fugiat-sunt-nihil-accusantium-harum.html</code></p>
        </div>
        </form>

                    <h2 id="post-PUTapi-v1-post--post_id-">Úprava příspěvku</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Upraví existující novinku. Odesílá se jen to, co se má změnit — ostatní pole zůstanou beze změny.</p>

<span id="example-requests-PUTapi-v1-post--post_id-">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/post/1';
$response = $client-&gt;put(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'title' =&gt; 'Upravený titulek novinky',
            'content' =&gt; 'Upravený obsah novinky.',
            'content_mode' =&gt; 2,
            'private' =&gt; false,
            'editorial' =&gt; 'architecto',
            'img_url' =&gt; 'http://www.bailey.biz/quos-velit-et-fugiat-sunt-nihil-accusantium-harum.html',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "https://demo.dalin.cz/api/v1/post/1" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"title\": \"Upravený titulek novinky\",
    \"content\": \"Upravený obsah novinky.\",
    \"content_mode\": 2,
    \"private\": false,
    \"editorial\": \"architecto\",
    \"img_url\": \"http:\\/\\/www.bailey.biz\\/quos-velit-et-fugiat-sunt-nihil-accusantium-harum.html\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/post/1"
);

const headers = {
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "title": "Upravený titulek novinky",
    "content": "Upravený obsah novinky.",
    "content_mode": 2,
    "private": false,
    "editorial": "architecto",
    "img_url": "http:\/\/www.bailey.biz\/quos-velit-et-fugiat-sunt-nihil-accusantium-harum.html"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-post--post_id-">
            <blockquote>
            <p>Example response (200, Post updated):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 4,
        &quot;user_id&quot;: 1,
        &quot;title&quot;: &quot;Upraven&yacute; titulek novinky&quot;,
        &quot;editorial&quot;: null,
        &quot;img_url&quot;: null,
        &quot;content&quot;: &quot;Upraven&yacute; obsah novinky.&quot;,
        &quot;content_mode&quot;: 2,
        &quot;private&quot;: 0,
        &quot;created_at&quot;: &quot;2026-08-27T08:00:00.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-08-27T08:05:00.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Post not found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;No query results for model [App\\Models\\Post] 999&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-v1-post--post_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-post--post_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-post--post_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-post--post_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-post--post_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-post--post_id-" data-method="PUT"
      data-path="api/v1/post/{post_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-post--post_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-post--post_id-"
                    onclick="tryItOut('PUTapi-v1-post--post_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-post--post_id-"
                    onclick="cancelTryOut('PUTapi-v1-post--post_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-post--post_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/post/{post_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="PUTapi-v1-post--post_id-"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-post--post_id-"
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
                              name="Accept"                data-endpoint="PUTapi-v1-post--post_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>post_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="post_id"                data-endpoint="PUTapi-v1-post--post_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the post. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>title</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="title"                data-endpoint="PUTapi-v1-post--post_id-"
               value="Upravený titulek novinky"
               data-component="body">
    <br>
<p>Post title. Example: <code>Upravený titulek novinky</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>content</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="content"                data-endpoint="PUTapi-v1-post--post_id-"
               value="Upravený obsah novinky."
               data-component="body">
    <br>
<p>Post content. Plain string for HTML/Markdown (content_mode 1/2), or a TipTap JSON document for content_mode 3. Example: <code>Upravený obsah novinky.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>content_mode</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="content_mode"                data-endpoint="PUTapi-v1-post--post_id-"
               value="2"
               data-component="body">
    <br>
<p>Content format: 1 = HTML, 2 = Markdown, 3 = TipTap JSON. Example: <code>2</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>private</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <label data-endpoint="PUTapi-v1-post--post_id-" style="display: none">
            <input type="radio" name="private"
                   value="true"
                   data-endpoint="PUTapi-v1-post--post_id-"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="PUTapi-v1-post--post_id-" style="display: none">
            <input type="radio" name="private"
                   value="false"
                   data-endpoint="PUTapi-v1-post--post_id-"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>true = internal post (members only), false = public. Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>editorial</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="editorial"                data-endpoint="PUTapi-v1-post--post_id-"
               value="architecto"
               data-component="body">
    <br>
<p>Optional editorial note. Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>img_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="img_url"                data-endpoint="PUTapi-v1-post--post_id-"
               value="http://www.bailey.biz/quos-velit-et-fugiat-sunt-nihil-accusantium-harum.html"
               data-component="body">
    <br>
<p>Optional cover image URL/path. Example: <code>http://www.bailey.biz/quos-velit-et-fugiat-sunt-nihil-accusantium-harum.html</code></p>
        </div>
        </form>

                <h1 id="page">Page</h1>

    <p>Obsahové stránky klubu (statický obsah).</p>

                                <h2 id="page-GETapi-v1-page">Seznam stránek</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Vrátí stránkovaný seznam obsahových stránek klubu.</p>

<span id="example-requests-GETapi-v1-page">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/page';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'from' =&gt; '2024-12-31',
            'to' =&gt; '2024-12-31',
            'page' =&gt; '1',
            'per_page' =&gt; '20',
            'status' =&gt; 'open',
            'content_category_id' =&gt; '1',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://demo.dalin.cz/api/v1/page?from=2024-12-31&amp;to=2024-12-31&amp;page=1&amp;per_page=20&amp;status=open&amp;content_category_id=1" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/page"
);

const params = {
    "from": "2024-12-31",
    "to": "2024-12-31",
    "page": "1",
    "per_page": "20",
    "status": "open",
    "content_category_id": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-page">
            <blockquote>
            <p>Example response (200, Example Page List):</p>
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
        &quot;first&quot;: &quot;http://localhost/api/v1/posts/list?page=1&quot;,
        &quot;last&quot;: null,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;current_page_url&quot;: &quot;http://localhost/api/v1/posts/list?page=1&quot;,
        &quot;from&quot;: 1,
        &quot;path&quot;: &quot;http://localhost/api/v1/posts/list&quot;,
        &quot;per_page&quot;: 20,
        &quot;to&quot;: 1
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-page" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-page"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-page"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-page" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-page">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-page" data-method="GET"
      data-path="api/v1/page"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-page', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-page"
                    onclick="tryItOut('GETapi-v1-page');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-page"
                    onclick="cancelTryOut('GETapi-v1-page');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-page"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/page</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="GETapi-v1-page"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-page"
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
                              name="Accept"                data-endpoint="GETapi-v1-page"
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
                              name="from"                data-endpoint="GETapi-v1-page"
               value="2024-12-31"
               data-component="query">
    <br>
<p>Date from in Y-M-D format Example: <code>2024-12-31</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="to"                data-endpoint="GETapi-v1-page"
               value="2024-12-31"
               data-component="query">
    <br>
<p>Date to in Y-M-D format Example: <code>2024-12-31</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-v1-page"
               value="1"
               data-component="query">
    <br>
<p>Page number for pagination Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-page"
               value="20"
               data-component="query">
    <br>
<p>Number of items per page. Defaults to 20. Example: <code>20</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-v1-page"
               value="open"
               data-component="query">
    <br>
<p>Filter by page status (open, close, draft, archive) Example: <code>open</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>content_category_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="content_category_id"                data-endpoint="GETapi-v1-page"
               value="1"
               data-component="query">
    <br>
<p>Filter by content category ID Example: <code>1</code></p>
            </div>
                </form>

                    <h2 id="page-GETapi-v1-page--page_id-">Detail stránky</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Vrátí detail konkrétní obsahové stránky podle ID.</p>

<span id="example-requests-GETapi-v1-page--page_id-">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'https://demo.dalin.cz/api/v1/page/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'x-apikey' =&gt; '{YOUR_API_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://demo.dalin.cz/api/v1/page/1" \
    --header "x-apikey: {YOUR_API_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://demo.dalin.cz/api/v1/page/1"
);

const headers = {
    "x-apikey": "{YOUR_API_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-page--page_id-">
            <blockquote>
            <p>Example response (200, Example with HTML content (content_format = 1)):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 2,
        &quot;user_id&quot;: 1,
        &quot;content_category_id&quot;: null,
        &quot;title&quot;: &quot;Kontakt&quot;,
        &quot;slug&quot;: &quot;kontakt&quot;,
        &quot;content&quot;: &quot;&lt;h1&gt;Kontakt&lt;/h1&gt;&lt;p&gt;This is HTML content&lt;/p&gt;&quot;,
        &quot;content_format&quot;: 1,
        &quot;picture_attachment&quot;: &quot;&quot;,
        &quot;status&quot;: &quot;open&quot;,
        &quot;weight&quot;: 30,
        &quot;page_menu&quot;: true,
        &quot;meta&quot;: null,
        &quot;created_at&quot;: &quot;2025-10-05T21:26:52.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2025-10-05T21:26:52.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (200, Example with Markdown content (content_format = 2)):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;user_id&quot;: 1,
        &quot;content_category_id&quot;: 1,
        &quot;title&quot;: &quot;O n&aacute;s&quot;,
        &quot;slug&quot;: &quot;o-nas&quot;,
        &quot;content&quot;: &quot;# O n&aacute;s\n\nToto je obsah str&aacute;nky v Markdown form&aacute;tu.&quot;,
        &quot;content_format&quot;: 2,
        &quot;picture_attachment&quot;: &quot;image.jpg&quot;,
        &quot;status&quot;: &quot;open&quot;,
        &quot;weight&quot;: 50,
        &quot;page_menu&quot;: true,
        &quot;meta&quot;: {
            &quot;description&quot;: &quot;Str&aacute;nka o n&aacute;s&quot;
        },
        &quot;created_at&quot;: &quot;2025-10-05T21:26:52.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2025-10-05T21:26:52.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (200, Example with TipTap JSON content (content_format = 3)):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 3,
        &quot;user_id&quot;: 1,
        &quot;content_category_id&quot;: 2,
        &quot;title&quot;: &quot;TipTap Page&quot;,
        &quot;slug&quot;: &quot;tiptap-page&quot;,
        &quot;content&quot;: {
            &quot;type&quot;: &quot;doc&quot;,
            &quot;content&quot;: [
                {
                    &quot;type&quot;: &quot;paragraph&quot;,
                    &quot;content&quot;: [
                        {
                            &quot;type&quot;: &quot;text&quot;,
                            &quot;text&quot;: &quot;This is TipTap JSON content&quot;
                        }
                    ]
                }
            ]
        },
        &quot;content_format&quot;: 3,
        &quot;picture_attachment&quot;: null,
        &quot;status&quot;: &quot;open&quot;,
        &quot;weight&quot;: 10,
        &quot;page_menu&quot;: false,
        &quot;meta&quot;: {
            &quot;keywords&quot;: &quot;tiptap, json&quot;
        },
        &quot;created_at&quot;: &quot;2025-10-05T21:26:52.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2025-10-05T21:26:52.000000Z&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-page--page_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-page--page_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-page--page_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-page--page_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-page--page_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of this API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-page--page_id-" data-method="GET"
      data-path="api/v1/page/{page_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-page--page_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-page--page_id-"
                    onclick="tryItOut('GETapi-v1-page--page_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-page--page_id-"
                    onclick="cancelTryOut('GETapi-v1-page--page_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-page--page_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/page/{page_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>x-apikey</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="x-apikey" class="auth-value"               data-endpoint="GETapi-v1-page--page_id-"
               value="{YOUR_API_KEY}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_API_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-page--page_id-"
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
                              name="Accept"                data-endpoint="GETapi-v1-page--page_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page_id"                data-endpoint="GETapi-v1-page--page_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the page. Example: <code>1</code></p>
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
