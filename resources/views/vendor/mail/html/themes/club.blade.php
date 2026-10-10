@php
    // Branded mail theme. Rendered as a Blade view (Illuminate\Mail\Markdown::render) and
    // inlined into the HTML by CssToInlineStyles, so only plain selectors work here —
    // responsive and dark-mode rules live in the <style> block of html/club/message.
    $brand = \App\Services\Mail\MailBranding::fromSettings();
@endphp
body, body *:not(html):not(style):not(br):not(tr):not(code) {
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {
    margin: 0;
    padding: 0;
    width: 100% !important;
    background-color: #eef0ed;
    color: {{ \App\Services\Mail\MailBranding::INK }};
    -webkit-text-size-adjust: none;
}

p {
    margin: 0;
}

img {
    border: 0;
}

.club-wrapper {
    background-color: #eef0ed;
}

.club-outer {
    padding: 24px 12px;
}

.club-card {
    width: 620px;
    max-width: 620px;
    background-color: #ffffff;
    border: 1px solid #dde4df;
    border-radius: 16px;
}

.club-stripe {
    height: 5px;
    background-color: {{ $brand->accent }};
    font-size: 0;
    line-height: 0;
    border-radius: 16px 16px 0 0;
}

.club-pad {
    padding-left: 30px;
    padding-right: 30px;
}

.club-header {
    padding-top: 22px;
    padding-bottom: 12px;
}

.club-logo {
    display: block;
    width: 42px;
    height: 42px;
    border-radius: 12px;
}

.club-initials {
    width: 42px;
    height: 42px;
    background-color: {{ $brand->accent }};
    color: {{ $brand->onAccent }};
    border-radius: 12px;
    font-size: 14px;
    font-weight: bold;
    text-align: center;
}

.club-brand {
    padding-left: 12px;
}

.club-name {
    display: block;
    font-size: 17px;
    font-weight: bold;
    color: {{ \App\Services\Mail\MailBranding::INK }};
}

.club-tagline {
    display: block;
    font-size: 12px;
    color: #52605d;
}

.club-wordmark {
    font-size: 13px;
    color: #52605d;
}

.club-hero {
    padding-top: 10px;
    padding-bottom: 16px;
}

.club-eyebrow {
    margin: 0 0 10px;
    font-size: 12px;
    font-weight: bold;
    letter-spacing: 1.6px;
    text-transform: uppercase;
    color: {{ \App\Services\Mail\MailBranding::INK }};
}

.club-title {
    margin: 0 0 12px;
    font-size: 25px;
    line-height: 1.2;
    font-weight: bold;
    color: {{ \App\Services\Mail\MailBranding::INK }};
}

.club-lead {
    margin: 0;
    font-size: 16px;
    line-height: 1.55;
    color: #52605d;
}

.club-body {
    padding-top: 12px;
    padding-bottom: 26px;
    font-size: 16px;
    line-height: 1.55;
    color: {{ \App\Services\Mail\MailBranding::INK }};
}

.club-body > p {
    margin: 0 0 12px;
    font-size: 16px;
    line-height: 1.55;
}

.club-body a {
    color: {{ \App\Services\Mail\MailBranding::INK }};
    text-decoration: underline;
}

.club-body > ul {
    margin: 0 0 12px;
    padding-left: 20px;
}

.club-body li {
    margin: 0 0 6px;
}

.club-body .club-section {
    margin: 22px 0 10px;
    font-size: 13px;
    font-weight: bold;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #52605d;
}

.club-event {
    border-bottom: 1px solid #dde4df;
}

.club-date {
    width: 50px;
    padding: 4px 0 4px 0;
    border-left: 3px solid {{ $brand->accent }};
    text-align: center;
    font-size: 12px;
    text-transform: uppercase;
    color: {{ \App\Services\Mail\MailBranding::INK }};
}

.club-date-day {
    font-size: 24px;
    line-height: 1.15;
    font-weight: bold;
}

.club-event-cell {
    padding: 16px 0;
}

.club-event-content {
    padding-left: 16px;
}

.club-event-name {
    margin: 0 0 6px;
    font-size: 17px;
    line-height: 1.35;
    font-weight: bold;
}

.club-body .club-event-name a {
    color: {{ \App\Services\Mail\MailBranding::INK }};
    text-decoration: none;
}

.club-meta {
    margin: 4px 0;
    font-size: 14px;
    line-height: 1.4;
    color: #52605d;
}

.club-icon {
    width: 16px;
    height: 16px;
    vertical-align: -3px;
    margin-right: 6px;
}

.club-pill {
    display: inline-block;
    margin-top: 8px;
    padding: 4px 9px;
    background-color: {{ $brand->accentSoft }};
    border-radius: 5px;
    font-size: 13px;
    font-weight: bold;
    color: {{ \App\Services\Mail\MailBranding::INK }};
}

.club-soft {
    background-color: #f5f7f3;
}

.club-facts {
    margin: 0 0 22px;
    border-radius: 12px;
}

.club-fact {
    width: 50%;
    padding: 12px 20px;
    vertical-align: top;
}

.club-fact-value {
    margin: 2px 0 0;
    font-size: 18px;
    font-weight: bold;
    color: {{ \App\Services\Mail\MailBranding::INK }};
}

.club-person {
    padding: 14px 0;
    border-bottom: 1px solid #dde4df;
}

.club-person-name {
    margin: 0 0 4px;
    font-size: 16px;
    font-weight: bold;
}

.club-person-extra {
    font-weight: normal;
    color: #52605d;
}

.club-status {
    margin: 0 0 16px;
    border-radius: 12px;
}

.club-status td {
    padding: 14px 20px;
}

.club-status-label {
    margin: 0;
    font-size: 17px;
    font-weight: bold;
}

.club-status-label .club-icon {
    width: 18px;
    height: 18px;
    vertical-align: -4px;
    margin-right: 8px;
}

.club-status-positive .club-status-label {
    color: #23704a;
}

.club-status-negative .club-status-label {
    color: #9f3035;
}

.club-note {
    margin: 20px 0;
    padding: 16px 18px;
    border-left: 3px solid {{ $brand->accent }};
    border-radius: 0 8px 8px 0;
}

.club-note-label {
    margin: 0 0 5px;
    font-size: 12px;
    color: #52605d;
}

.club-actions {
    margin-top: 22px;
}

.club-body .club-button {
    display: inline-block;
    padding: 12px 20px;
    background-color: {{ $brand->accent }};
    border-radius: 8px;
    font-size: 15px;
    font-weight: bold;
    line-height: 1.3;
    color: {{ $brand->onAccent }};
    text-decoration: none;
}

.club-secondary {
    padding-left: 18px;
    font-size: 14px;
}

.club-body .club-secondary a {
    color: {{ \App\Services\Mail\MailBranding::INK }};
    text-decoration: underline;
}

.club-body .club-secondary-negative a {
    color: #9f3035;
}

.club-body .club-fine {
    margin: 16px 0 0;
    font-size: 13px;
    line-height: 1.5;
    color: #52605d;
}

.club-footer {
    padding-top: 20px;
    padding-bottom: 22px;
    border-top: 1px solid #dde4df;
    font-size: 12px;
    line-height: 1.5;
    color: #52605d;
}

.club-footer p {
    margin: 0 0 6px;
}

.club-footer a {
    color: #52605d;
    text-decoration: underline;
}
