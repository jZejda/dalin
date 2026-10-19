<?php

declare(strict_types=1);

namespace App\Services\Payment;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Str;

/**
 * Czech "QR Platba" (SPAYD 1.0, https://qr-platba.cz/pro-vyvojare/specifikace-formatu/) for a bank transfer.
 */
final readonly class QrPayment
{
    private const int MESSAGE_MAX_LENGTH = 60;

    public function __construct(
        public string $iban,
        public float $amount,
        public string $currency = 'CZK',
        public ?string $variableSymbol = null,
        public ?string $message = null,
    ) {
    }

    /**
     * Null when the account is not a Czech domestic one or there is nothing to pay.
     */
    public static function forCzechAccount(
        string $account,
        float $amount,
        string $currency = 'CZK',
        ?string $variableSymbol = null,
        ?string $message = null,
    ): ?self {
        $bankAccount = CzechBankAccount::parse($account);
        if ($bankAccount === null || $amount <= 0) {
            return null;
        }

        return new self($bankAccount->toIban(), $amount, $currency, $variableSymbol, $message);
    }

    public function toSpayd(): string
    {
        $fields = [
            'SPD*1.0',
            'ACC:'.$this->iban,
            'AM:'.number_format($this->amount, 2, '.', ''),
            'CC:'.Str::upper($this->currency),
        ];

        // X-VS is at most 10 digits; ORIS hands out numeric symbols, anything else is left out
        if ($this->variableSymbol !== null && preg_match('/^\d{1,10}$/', $this->variableSymbol) === 1) {
            $fields[] = 'X-VS:'.$this->variableSymbol;
        }

        if (filled($this->message)) {
            // '*' separates the fields; plain ASCII keeps older banking apps happy
            $message = Str::limit(str_replace('*', '', Str::ascii($this->message)), self::MESSAGE_MAX_LENGTH, '');
            $fields[] = 'MSG:'.Str::upper($message);
        }

        return implode('*', $fields);
    }

    /**
     * PNG image of the QR code (raw bytes, for embedding into an e-mail), or null without the GD extension.
     */
    public function toPng(int $scale = 5): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $options = new QROptions([
            // 5.0 picks the renderer by outputType; outputInterface is what later releases use
            'outputType' => QROutputInterface::GDIMAGE_PNG,
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64' => false,
            'eccLevel' => EccLevel::M,
            'scale' => $scale,
            'quietzoneSize' => 2,
        ]);

        return (new QRCode($options))->render($this->toSpayd());
    }

    /**
     * SVG data URI of the QR code for an <img> on a web page (needs no image extension).
     */
    public function toSvgDataUri(): string
    {
        $options = new QROptions([
            'outputType' => QROutputInterface::MARKUP_SVG,
            'outputInterface' => QRMarkupSVG::class,
            'outputBase64' => true,
            'eccLevel' => EccLevel::M,
            'quietzoneSize' => 2,
            'svgAddXmlHeader' => false,
        ]);

        return (new QRCode($options))->render($this->toSpayd());
    }
}
