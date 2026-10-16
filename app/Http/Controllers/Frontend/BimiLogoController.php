<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Services\Mail\BimiLogoValidator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Serves the BIMI e-mail logo at a stable URL for the `default._bimi` DNS record,
 * so replacing the upload in club settings needs no DNS change.
 */
class BimiLogoController extends Controller
{
    public function __invoke(BimiLogoValidator $validator): Response
    {
        $path = AppSetting::getSeoBimiLogoPath();
        $svg = $path !== null ? Storage::disk('public')->get($path) : null;

        abort_if($svg === null || ! $validator->isValid($svg), 404);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'",
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
