<?php

declare(strict_types=1);

use Mantraideas\LaravelFonepay\DTOs\TokenDTO;

test('create token dto from array with all fields', function () {
    $data = [
        'username' => 'merchant_user',
        'accessToken' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9',
        'refreshToken' => 'dGhpcyBpcyBhIHJlZnJlc2ggdG9rZW4',
        'tokenType' => 'Bearer',
        'expiresIn' => 3600,
    ];

    $dto = TokenDTO::fromArray($data);

    expect($dto)->toBeInstanceOf(TokenDTO::class);
    expect($dto->username)->toBe('merchant_user');
    expect($dto->accessToken)->toBe('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9');
    expect($dto->refreshToken)->toBe('dGhpcyBpcyBhIHJlZnJlc2ggdG9rZW4');
    expect($dto->tokenType)->toBe('Bearer');
    expect($dto->expiresIn)->toBe(3600);
});
