<?php

declare(strict_types=1);

namespace Mantraideas\LaravelFonepay\DTOs;

class BankDTO
{
    public function __construct(
        public string $bankName,
        public string $bankCode,
        public string $bankIcon,
        public string $packageName,
        public string $intentScheme,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            // @phpstan-ignore argument.type
            bankName: strval($data['bankName'] ?? ''),
            // @phpstan-ignore argument.type
            bankCode: strval($data['bankCode'] ?? ''),
            // @phpstan-ignore argument.type
            bankIcon: strval($data['bankIcon'] ?? ''),
            // @phpstan-ignore argument.type
            packageName: strval($data['packageName'] ?? ''),
            // @phpstan-ignore argument.type
            intentScheme: strval($data['intentScheme'] ?? ''),
        );
    }
}
