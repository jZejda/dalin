<x-mail::message>

## Hromadná zpráva

Upozornění z interního systemu {{ Config::get('site-config.club.abbr') }} na vybrané uživatele systému.

@component('mail::divider')
Zpráva od uživatele **{{ $user->name }}**.

- Zpráva zaslána dne: **{{ \Carbon\Carbon::now()->format('d.h.Y H:i') ?? ''}}**
@endcomponent

{{ $content }}

</x-mail::message>

