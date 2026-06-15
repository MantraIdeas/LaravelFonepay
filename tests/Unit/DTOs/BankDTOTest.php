<?php

declare(strict_types=1);

use Mantraideas\LaravelFonepay\DTOs\BankDTO;

test('create bank dto from array with all fields', function () {
    $data = [
        'bankName' => 'Nepal Bank Limited',
        'bankCode' => 'NBL',
        'bankIcon' => 'https://example.com/nbl.png',
        'packageName' => 'com.nbl.app',
        'intentScheme' => 'nbl://payment',
    ];

    $dto = BankDTO::fromArray($data);

    expect($dto)->toBeInstanceOf(BankDTO::class);
    expect($dto->bankName)->toBe('Nepal Bank Limited');
    expect($dto->bankCode)->toBe('NBL');
    expect($dto->bankIcon)->toBe('https://example.com/nbl.png');
    expect($dto->packageName)->toBe('com.nbl.app');
    expect($dto->intentScheme)->toBe('nbl://payment');
});
