<?php

declare(strict_types=1);

namespace Mantraideas\LaravelFonepay\DTOs;

class GenerateQrCodeDTO
{
    public function __construct(
        public float $amount,
        public string $billId,
        public string $terminalId,
        public string $paymentMode,
        public string $referenceLabel,
        public string $qrType,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'billId' => $this->billId,
            'terminalId' => $this->terminalId,
            'paymentMode' => $this->paymentMode,
            'referenceLabel' => $this->referenceLabel,
            'qrType' => $this->qrType,
        ];
    }
}
