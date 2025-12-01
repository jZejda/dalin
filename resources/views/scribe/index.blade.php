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
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.6.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.6.0.js") }}"></script>

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
                                                    <li class="tocify-item level-2" data-unique="v1-post">
                                <a href="#v1-post">POST</a>
                            </li>
                                                            <ul id="tocify-subheader-v1-post" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-post-list">
                                            <a href="#v1-GETapi-v1-post-list">GET api/v1/post/list</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-post--post_id-">
                                            <a href="#v1-GETapi-v1-post--post_id-">GET api/v1/post/{post_id}</a>
                                        </li>
                                                                    </ul>
                                                                                <li class="tocify-item level-2" data-unique="v1-page">
                                <a href="#v1-page">PAGE</a>
                            </li>
                                                            <ul id="tocify-subheader-v1-page" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-page-list">
                                            <a href="#v1-GETapi-v1-page-list">GET api/v1/page/list</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="v1-GETapi-v1-page--page_id-">
                                            <a href="#v1-GETapi-v1-page--page_id-">GET api/v1/page/{page_id}</a>
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
        <li>Last updated: December 2, 2025</li>
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

                        <h2 id="v1-post">POST</h2>
                                        <p>
                    <p>News</p>
                </p>
                                        <h2 id="v1-GETapi-v1-post-list">GET api/v1/post/list</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-post-list">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/v1/post/list';
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
    --get "http://localhost/api/v1/post/list?from=2024-12-31&amp;to=2024-12-31&amp;page=1&amp;per_page=architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/post/list"
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

<span id="example-responses-GETapi-v1-post-list">
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
<span id="execution-results-GETapi-v1-post-list" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-post-list"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-post-list"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-post-list" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-post-list">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-post-list" data-method="GET"
      data-path="api/v1/post/list"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-post-list', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-post-list"
                    onclick="tryItOut('GETapi-v1-post-list');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-post-list"
                    onclick="cancelTryOut('GETapi-v1-post-list');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-post-list"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/post/list</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-post-list"
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
                              name="Accept"                data-endpoint="GETapi-v1-post-list"
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
                              name="from"                data-endpoint="GETapi-v1-post-list"
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
                              name="to"                data-endpoint="GETapi-v1-post-list"
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
               step="any"               name="page"                data-endpoint="GETapi-v1-post-list"
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
                              name="per_page"                data-endpoint="GETapi-v1-post-list"
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
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
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
                                        <h2 id="v1-GETapi-v1-page-list">GET api/v1/page/list</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-page-list">
<blockquote>Example request:</blockquote>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/v1/page/list';
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
    --get "http://localhost/api/v1/page/list?from=2024-12-31&amp;to=2024-12-31&amp;page=1&amp;per_page=20&amp;status=open&amp;content_category_id=1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/page/list"
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

<span id="example-responses-GETapi-v1-page-list">
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
<span id="execution-results-GETapi-v1-page-list" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-page-list"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-page-list"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-page-list" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-page-list">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-page-list" data-method="GET"
      data-path="api/v1/page/list"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-page-list', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-page-list"
                    onclick="tryItOut('GETapi-v1-page-list');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-page-list"
                    onclick="cancelTryOut('GETapi-v1-page-list');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-page-list"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/page/list</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-page-list"
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
                              name="Accept"                data-endpoint="GETapi-v1-page-list"
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
                              name="from"                data-endpoint="GETapi-v1-page-list"
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
                              name="to"                data-endpoint="GETapi-v1-page-list"
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
               step="any"               name="page"                data-endpoint="GETapi-v1-page-list"
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
               step="any"               name="per_page"                data-endpoint="GETapi-v1-page-list"
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
                              name="status"                data-endpoint="GETapi-v1-page-list"
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
               step="any"               name="content_category_id"                data-endpoint="GETapi-v1-page-list"
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
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
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
