<?php

declare(strict_types=1);

use Mantraideas\LaravelFonepay\DTOs\GenerateQrCodeDTO;

test('create generate qr code dto with all properties', function () {
    $dto = new GenerateQrCodeDTO(
        amount: 100.00,
        billId: 'BILL-001',
        terminalId: 'TERM-001',
        paymentMode: 'QR',
        referenceLabel: 'REF-001',
        qrType: 'INTENT_QR',
    );

    expect($dto->amount)->toBe(100.00);
    expect($dto->billId)->toBe('BILL-001');
    expect($dto->terminalId)->toBe('TERM-001');
    expect($dto->paymentMode)->toBe('QR');
    expect($dto->referenceLabel)->toBe('REF-001');
    expect($dto->qrType)->toBe('INTENT_QR');
});

test('generate qr code dto to array returns all keys', function () {
    $dto = new GenerateQrCodeDTO(
        amount: 250.50,
        billId: 'BILL-002',
        terminalId: 'TERM-002',
        paymentMode: 'QR',
        referenceLabel: 'REF-002',
        qrType: 'STATIC_QR',
    );

    $array = $dto->toArray();

    expect($array)->toBe([
        'amount' => 250.50,
        'billId' => 'BILL-002',
        'terminalId' => 'TERM-002',
        'paymentMode' => 'QR',
        'referenceLabel' => 'REF-002',
        'qrType' => 'STATIC_QR',
    ]);
});

test('generate qr code dto to array is immutable', function () {
    $dto = new GenerateQrCodeDTO(
        amount: 100.00,
        billId: 'BILL-001',
        terminalId: 'TERM-001',
        paymentMode: 'QR',
        referenceLabel: 'REF-001',
        qrType: 'INTENT_QR',
    );

    $array = $dto->toArray();
    $array['amount'] = 999.99;

    expect($dto->amount)->toBe(100.00);
});
