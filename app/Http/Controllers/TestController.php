<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class TestController extends Controller
{
    public function test(): void
    {
        dd('done');
    }
}
