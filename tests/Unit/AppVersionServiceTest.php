<?php

declare(strict_types=1);

use App\Services\AppVersionService;

beforeEach(function (): void {
    $this->basePath = sys_get_temp_dir().'/dalin-version-'.uniqid();
    mkdir($this->basePath.'/.git', recursive: true);
});

afterEach(function (): void {
    exec('rm -rf '.escapeshellarg($this->basePath));
});

test('version and tag come from config', function (): void {
    config()->set('version.version', '13.1.0');

    $service = new AppVersionService($this->basePath);

    expect($service->version())->toBe('13.1.0')
        ->and($service->tag())->toBe('v13.1.0');
});

test('version falls back when config is missing', function (): void {
    config()->set('version.version', null);

    expect((new AppVersionService($this->basePath))->version())->toBe('0.0.0');
});

test('build is read from the REVISION file written by Deployer', function (): void {
    file_put_contents($this->basePath.'/REVISION', "7d09353aa1b2c3d4e5f60718293a4b5c6d7e8f90\n");

    $service = new AppVersionService($this->basePath);

    expect($service->revision())->toBe('7d09353aa1b2c3d4e5f60718293a4b5c6d7e8f90')
        ->and($service->build())->toBe('7d09353')
        ->and($service->builtAt())->not->toBeNull();
});

test('build falls back to the git branch ref when REVISION is missing', function (): void {
    file_put_contents($this->basePath.'/.git/HEAD', "ref: refs/heads/v13.x\n");
    mkdir($this->basePath.'/.git/refs/heads', recursive: true);
    file_put_contents($this->basePath.'/.git/refs/heads/v13.x', "abcdef1234567890abcdef1234567890abcdef12\n");

    expect((new AppVersionService($this->basePath))->build())->toBe('abcdef1');
});

test('build falls back to packed-refs when the loose ref is gone', function (): void {
    file_put_contents($this->basePath.'/.git/HEAD', "ref: refs/heads/v13.x\n");
    file_put_contents(
        $this->basePath.'/.git/packed-refs',
        "# pack-refs with: peeled fully-peeled sorted\n".
        "1111111111111111111111111111111111111111 refs/heads/main\n".
        "2222222222222222222222222222222222222222 refs/heads/v13.x\n"
    );

    expect((new AppVersionService($this->basePath))->build())->toBe('2222222');
});

test('detached HEAD is used as the revision', function (): void {
    file_put_contents($this->basePath.'/.git/HEAD', "3333333333333333333333333333333333333333\n");

    expect((new AppVersionService($this->basePath))->build())->toBe('3333333');
});

test('build is null when neither REVISION nor git is available', function (): void {
    $service = new AppVersionService($this->basePath);

    expect($service->revision())->toBeNull()
        ->and($service->build())->toBeNull()
        ->and($service->builtAt())->toBeNull();
});

test('summary joins tag and build, and degrades to the tag alone', function (): void {
    config()->set('version.version', '13.1.0');

    file_put_contents($this->basePath.'/REVISION', "7d09353aa1b2c3d4e5f60718293a4b5c6d7e8f90\n");
    expect((new AppVersionService($this->basePath))->summary())->toBe('v13.1.0 · build 7d09353');

    unlink($this->basePath.'/REVISION');
    expect((new AppVersionService($this->basePath))->summary())->toBe('v13.1.0');
});
