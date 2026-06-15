<?php

declare(strict_types=1);

use Mantraideas\LaravelFonepay\DTOs\QrCodeDTO;

test('create qr code dto from array with all fields', function () {
    $data = [
        'qrString' => 'qr-string-content',
        'qrDisplayName' => 'Test Merchant',
        'status' => 'SUCCESS',
        'terminalId' => 123456,
        'prn' => 'PRN-001',
        'qrMessage' => 'QR Code generated successfully',
        'terminalName' => 'Terminal Alpha',
        'websocketId' => 'WS-001',
        'location' => 'Kathmandu',
        'fonepayPanNumber' => 'PAN-123456',
    ];

    $dto = QrCodeDTO::fromArray($data);

    expect($dto)->toBeInstanceOf(QrCodeDTO::class);
    expect($dto->qrString)->toBe('qr-string-content');
    expect($dto->qrDisplayName)->toBe('Test Merchant');
    expect($dto->status)->toBe('SUCCESS');
    expect($dto->terminalId)->toBe(123456);
    expect($dto->prn)->toBe('PRN-001');
    expect($dto->qrMessage)->toBe('QR Code generated successfully');
    expect($dto->terminalName)->toBe('Terminal Alpha');
    expect($dto->webSocketId)->toBe('WS-001');
    expect($dto->location)->toBe('Kathmandu');
    expect($dto->fonePayPanNumber)->toBe('PAN-123456');
});
