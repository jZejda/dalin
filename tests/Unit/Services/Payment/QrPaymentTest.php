<?php

declare(strict_types=1);

use App\Services\Payment\CzechBankAccount;
use App\Services\Payment\QrPayment;

it('converts a Czech account number to IBAN', function (string $account, string $iban): void {
    expect(CzechBankAccount::parse($account)?->toIban())->toBe($iban);
})->with([
    'with prefix' => ['19-2000145399/0800', 'CZ6508000000192000145399'],
    'without prefix' => ['2901296231/2010', 'CZ3520100000002901296231'],
    'with spaces' => [' 19-2000145399 / 0800 ', 'CZ6508000000192000145399'],
]);

it('rejects what is not a Czech account number', function (string $account): void {
    expect(CzechBankAccount::parse($account))->toBeNull();
})->with(['CZ6508000000192000145399', '2000145399', '2000145399/80', 'abc/0800', '']);

it('builds the SPAYD string with amount, variable symbol and an ASCII message', function (): void {
    $payment = QrPayment::forCzechAccount('2901296231/2010', 160, 'CZK', '2631104', 'Startovné ABM *Harrachov*');

    expect($payment?->toSpayd())
        ->toBe('SPD*1.0*ACC:CZ3520100000002901296231*AM:160.00*CC:CZK*X-VS:2631104*MSG:STARTOVNE ABM HARRACHOV');
});

it('leaves out a variable symbol that is not up to 10 digits and caps the message', function (): void {
    $spayd = QrPayment::forCzechAccount('2901296231/2010', 99.5, 'CZK', 'VS-12', str_repeat('a', 80))?->toSpayd();

    expect($spayd)->not->toContain('X-VS')
        ->toContain('AM:99.50')
        ->toContain('MSG:'.str_repeat('A', 60))
        ->not->toContain(str_repeat('A', 61));
});

it('has no QR payment without a Czech account or an amount due', function (string $account, float $amount): void {
    expect(QrPayment::forCzechAccount($account, $amount))->toBeNull();
})->with([
    'foreign account' => ['DE89370400440532013000', 100],
    'nothing to pay' => ['2901296231/2010', 0],
    'overpaid' => ['2901296231/2010', -50],
]);

it('renders the QR code as a PNG and as an SVG data URI', function (): void {
    $payment = new QrPayment('CZ3520100000002901296231', 160);

    expect(substr((string) $payment->toPng(), 0, 8))->toBe("\x89PNG\r\n\x1a\n")
        ->and($payment->toSvgDataUri())->toStartWith('data:image/svg+xml;base64,');
});
