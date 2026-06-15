<?php

declare(strict_types=1);

use Mantraideas\LaravelFonepay\Facades\Fonepay;
use Mantraideas\LaravelFonepay\Services\FonepayService;

test('facade accessor returns fonepay service class', function () {
    expect(Fonepay::getFacadeRoot())->toBeNull();
});

test('facade accessor name is fonepay service class', function () {
    $reflection = new ReflectionMethod(Fonepay::class, 'getFacadeAccessor');
    $reflection->setAccessible(true);

    $result = $reflection->invoke(null);

    expect($result)->toBe(FonepayService::class);
});
