<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Override;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        // A cached config ignores phpunit.xml's env overrides, so the suite would silently run against the dev DB
        if ($this->app->configurationIsCached()) {
            throw new RuntimeException('Configuration is cached (bootstrap/cache/config.php) — tests would hit the dev database. Run `make clear` first.');
        }
    }
}
