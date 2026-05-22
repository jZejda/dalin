<?php

declare(strict_types=1);

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeDemoController extends Controller
{
    public function index(): View
    {
        $logoPath = public_path('images/qr-logo.png');
        $hasLogo = file_exists($logoPath);

        //$url = 'https://abmbrno.cz/vysledky/7-jml-2026-lovcicky-vysledky';
        $url = 'https://oresults.eu/events/3240';

        $qr1 = QrCode::format('png')->size(1000)->errorCorrection('H')->eye('circle');
        if ($hasLogo) { $qr1->merge($logoPath, 0.25, true); }
        $basic = base64_encode((string) $qr1->generate($url));

        $qr2 = QrCode::format('png')->size(1000)->errorCorrection('H')->style('dot')->gradient(80, 0, 160, 220, 60, 0, 'radial');
        if ($hasLogo) { $qr2->merge($logoPath, 0.25, true); }
        $gradient = base64_encode((string) $qr2->generate($url));

        $qr3 = QrCode::format('png')->size(1000)->errorCorrection('H')->style('round')->eye('circle')->color(10, 50, 180)->backgroundColor(240, 245, 255);
        if ($hasLogo) { $qr3->merge($logoPath, 0.25, true); }
        $rounded = base64_encode((string) $qr3->generate($url));

        $qr4 = QrCode::format('png')->size(1000)->errorCorrection('H')->style('dot')->eye('circle')->gradient(200, 150, 0, 255, 200, 50, 'diagonal')->backgroundColor(15, 15, 30);
        if ($hasLogo) { $qr4->merge($logoPath, 0.25, true); }
        $dark = base64_encode((string) $qr4->generate($url));

        return view('pages.frontend.qr-demo', compact('basic', 'gradient', 'rounded', 'dark', 'hasLogo', 'url'))
            ->with('sponsorSectionId', 0);
    }
}
