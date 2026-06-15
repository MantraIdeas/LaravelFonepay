<?php

declare(strict_types=1);

use Mantraideas\LaravelFonepay\DTOs\StatusDTO;

test('create status dto from array with trace id', function () {
    $data = [
        'prn' => 'PRN-001',
        'merchantCode' => 'MER-001',
        'paymentStatus' => 'COMPLETED',
        'fonepayTraceId' => 98765,
        'requestedAmount' => '100.00',
        'totalTransactionAmount' => '100.00',
        'paymentMessage' => 'Payment successful',
    ];

    $dto = StatusDTO::fromArray($data);

    expect($dto->referenceLabel)->toBe('PRN-001');
    expect($dto->merchantCode)->toBe('MER-001');
    expect($dto->paymentStatus)->toBe('COMPLETED');
    expect($dto->fonepayTraceId)->toBe(98765);
    expect($dto->requestedAmount)->toBe('100.00');
    expect($dto->totalTransactionAmount)->toBe('100.00');
    expect($dto->paymentMessage)->toBe('Payment successful');
});

test('create status dto from array with null trace id', function () {
    $data = [
        'prn' => 'PRN-002',
        'merchantCode' => 'MER-002',
        'paymentStatus' => 'PENDING',
        'fonepayTraceId' => null,
        'requestedAmount' => '200.00',
        'totalTransactionAmount' => '0.00',
        'paymentMessage' => 'Payment pending',
    ];

    $dto = StatusDTO::fromArray($data);

    expect($dto->referenceLabel)->toBe('PRN-002');
    expect($dto->fonepayTraceId)->toBeNull();
    expect($dto->paymentStatus)->toBe('PENDING');
});

test('create status dto from array with missing trace id', function () {
    $data = [
        'prn' => 'PRN-003',
        'merchantCode' => 'MER-003',
        'paymentStatus' => 'FAILED',
        'requestedAmount' => '300.00',
        'totalTransactionAmount' => '0.00',
        'paymentMessage' => 'Payment failed',
    ];

    $dto = StatusDTO::fromArray($data);

    expect($dto->fonepayTraceId)->toBeNull();
    expect($dto->referenceLabel)->toBe('PRN-003');
});
