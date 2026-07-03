<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} | Doprava</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f3f4f6; margin: 0; padding: 2rem 1rem; }
        .card { max-width: 32rem; margin: 3rem auto; background: #fff; border-radius: 0.75rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,.1); text-align: center; }
        .icon { font-size: 3rem; }
        h1 { font-size: 1.25rem; margin: 1rem 0 .5rem; color: #111827; }
        p { color: #4b5563; margin: .25rem 0; }
        .detail { margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; font-size: .9rem; }
    </style>
</head>
<body>
<div class="card">
    @if($result === 'approved')
        <div class="icon">✅</div>
        <h1>Žádost o spolujízdu byla schválena</h1>
        <p>Žadatel dostal potvrzení e-mailem a místa jsou zablokovaná.</p>
    @elseif($result === 'rejected')
        <div class="icon">🚫</div>
        <h1>Žádost o spolujízdu byla zamítnuta</h1>
        <p>Žadatel dostal informaci e-mailem.</p>
    @elseif($result === 'rejected_capacity')
        <div class="icon">⚠️</div>
        <h1>Žádost nešlo schválit — není dost volných míst</h1>
        <p>Žádost byla zamítnuta a žadatel dostal informaci e-mailem.</p>
    @else
        <div class="icon">ℹ️</div>
        <h1>Žádost už je vyřízená</h1>
        <p>Aktuální stav: <strong>{{ $transportRequest->status->label() }}</strong></p>
    @endif

    <div class="detail">
        <p>Závod: <strong>{{ $transportRequest->transportOffer?->sportEvent?->name }}</strong></p>
        <p>Žadatel: {{ $transportRequest->user?->name }} | Směr: {{ $transportRequest->direction->label() }} | Míst: {{ $transportRequest->seats }}</p>
    </div>
</div>
</body>
</html>
