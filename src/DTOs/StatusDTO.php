<?php

declare(strict_types=1);

namespace Mantraideas\LaravelFonepay\DTOs;

class StatusDTO
{
    public function __construct(
        public string $referenceLabel,
        public string $merchantCode,
        public string $paymentStatus,
        public string $requestedAmount,
        public string $totalTransactionAmount,
        public string $paymentMessage,
        public ?int $fonepayTraceId = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            // @phpstan-ignore argument.type
            referenceLabel: strval($data['prn'] ?? ''),
            // @phpstan-ignore argument.type
            merchantCode: strval($data['merchantCode'] ?? ''),
            // @phpstan-ignore argument.type
            paymentStatus: strval($data['paymentStatus'] ?? ''),
            // @phpstan-ignore argument.type
            requestedAmount: strval($data['requestedAmount'] ?? ''),
            // @phpstan-ignore argument.type
            totalTransactionAmount: strval($data['totalTransactionAmount'] ?? ''),
            // @phpstan-ignore argument.type
            paymentMessage: strval($data['paymentMessage'] ?? ''),
            // @phpstan-ignore argument.type
            fonepayTraceId: isset($data['fonepayTraceId']) ? intval($data['fonepayTraceId']) : null,
        );
    }
}
