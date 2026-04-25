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
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.9.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.9.0.js") }}"></script>

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
                    <ul id="tocify-header-v1" class="tocify-header">
                <li class="tocify-item level-1" data-unique="v1">
                    <a href="#v1">V1</a>
                </li>
                                    <ul id="tocify-subheader-v1" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="v1-user">
                                <a href="#v1-user">USER</a>
                            </li>
                                                            <ul id="tocify-subheader-v1-user" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-user-race-profiles">
                                            <a href="#v1-GETapi-v1-user-race-profiles">GET api/v1/user/race-profiles</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-user-entry">
                                            <a href="#v1-GETapi-v1-user-entry">GET api/v1/user/entry</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-user-credit-balance">
                                            <a href="#v1-GETapi-v1-user-credit-balance">GET api/v1/user/credit-balance</a>
                                        </li>
                                                                    </ul>
                                                                                <li class="tocify-item level-2" data-unique="v1-post">
                                <a href="#v1-post">POST</a>
                            </li>
                                                            <ul id="tocify-subheader-v1-post" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-post">
                                            <a href="#v1-GETapi-v1-post">GET api/v1/post</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-post--post_id-">
                                            <a href="#v1-GETapi-v1-post--post_id-">GET api/v1/post/{post_id}</a>
                                        </li>
                                                                    </ul>
                                                                                <li class="tocify-item level-2" data-unique="v1-page">
                                <a href="#v1-page">PAGE</a>
                            </li>
                                                            <ul id="tocify-subheader-v1-page" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-page">
                                            <a href="#v1-GETapi-v1-page">GET api/v1/page</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-page--page_id-">
                                            <a href="#v1-GETapi-v1-page--page_id-">GET api/v1/page/{page_id}</a>
                                        </li>
                                                                    </ul>
                                                                                <li class="tocify-item level-2" data-unique="v1-sport-event">
                                <a href="#v1-sport-event">SPORT EVENT</a>
                            </li>
                                                            <ul id="tocify-subheader-v1-sport-event" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-sport-event">
                                            <a href="#v1-GETapi-v1-sport-event">GET api/v1/sport-event</a>
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
        <li>Last updated: April 24, 2026</li>
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

        <h1 id="v1">V1</h1>

    <p>APIs V1</p>

                        <h2 id="v1-user">USER</h2>
                                        <p>
                    <p>Authenticated user overview endpoints</p>
                </p>
                                        <h2 id="v1-GETapi-v1-user-race-profiles">GET api/v1/user/race-profiles</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-user-race-profiles">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/v1/user/race-profiles';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
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
    --get "http://localhost/api/v1/user/race-profiles?all=" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/user/race-profiles"
);

const params = {
    "all": "0",
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
      data-authed="0"
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

                    <h2 id="v1-GETapi-v1-user-entry">GET api/v1/user/entry</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-user-entry">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/v1/user/entry';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
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
            'from' =&gt; '2026-04-24',
            'to' =&gt; '2026-04-24',
            'per_page' =&gt; 1,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/user/entry?from=2026-01-01&amp;to=2026-12-31&amp;page=1&amp;per_page=20" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"from\": \"2026-04-24\",
    \"to\": \"2026-04-24\",
    \"per_page\": 1
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/user/entry"
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
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "from": "2026-04-24",
    "to": "2026-04-24",
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
      data-authed="0"
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
               value="2026-04-24"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>Y-m-d</code>. Example: <code>2026-04-24</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="to"                data-endpoint="GETapi-v1-user-entry"
               value="2026-04-24"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>Y-m-d</code>. Example: <code>2026-04-24</code></p>
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
<p>Must be at least 1. Must not be greater than 100. Example: <code>1</code></p>
        </div>
        </form>

                    <h2 id="v1-GETapi-v1-user-credit-balance">GET api/v1/user/credit-balance</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-user-credit-balance">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/v1/user/credit-balance';
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
    --get "http://localhost/api/v1/user/credit-balance" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/user/credit-balance"
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
      data-authed="0"
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

                                <h2 id="v1-post">POST</h2>
                                        <p>
                    <p>News</p>
                </p>
                                        <h2 id="v1-GETapi-v1-post">GET api/v1/post</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-post">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/v1/post';
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
    --get "http://localhost/api/v1/post?from=2024-12-31&amp;to=2024-12-31&amp;page=1&amp;per_page=architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/post"
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
      data-authed="0"
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

                    <h2 id="v1-GETapi-v1-post--post_id-">GET api/v1/post/{post_id}</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-post--post_id-">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/v1/post/1';
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
    --get "http://localhost/api/v1/post/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/post/1"
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
      data-authed="0"
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

                                <h2 id="v1-page">PAGE</h2>
                                        <p>
                    <p>Page</p>
                </p>
                                        <h2 id="v1-GETapi-v1-page">GET api/v1/page</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-page">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/v1/page';
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
    --get "http://localhost/api/v1/page?from=2024-12-31&amp;to=2024-12-31&amp;page=1&amp;per_page=20&amp;status=open&amp;content_category_id=1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/page"
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
      data-authed="0"
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

                    <h2 id="v1-GETapi-v1-page--page_id-">GET api/v1/page/{page_id}</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-page--page_id-">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/v1/page/1';
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
    --get "http://localhost/api/v1/page/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/page/1"
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
      data-authed="0"
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

                                <h2 id="v1-sport-event">SPORT EVENT</h2>
                                        <p>
                    <p>Sport events and event category options</p>
                </p>
                                        <h2 id="v1-GETapi-v1-sport-event">GET api/v1/sport-event</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-sport-event">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/v1/sport-event';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
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
            'from' =&gt; '2026-04-24',
            'to' =&gt; '2026-04-24',
            'class_definition_id' =&gt; 16,
            'per_page' =&gt; 22,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/sport-event?from=2026-01-01&amp;to=2026-12-31&amp;event_type=race&amp;class_definition_id=15&amp;page=1&amp;per_page=20" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"from\": \"2026-04-24\",
    \"to\": \"2026-04-24\",
    \"class_definition_id\": 16,
    \"per_page\": 22
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/sport-event"
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
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "from": "2026-04-24",
    "to": "2026-04-24",
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
      data-authed="0"
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
               value="2026-04-24"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>Y-m-d</code>. Example: <code>2026-04-24</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="to"                data-endpoint="GETapi-v1-sport-event"
               value="2026-04-24"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>Y-m-d</code>. Example: <code>2026-04-24</code></p>
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
<p>Must be at least 1. Must not be greater than 100. Example: <code>22</code></p>
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
