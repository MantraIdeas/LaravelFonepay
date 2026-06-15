<?php

declare(strict_types=1);

namespace Mantraideas\LaravelFonepay\DTOs;

class QrCodeDTO
{
    public function __construct(
        public string $qrString,
        public string $qrDisplayName,
        public string $status,
        public int $terminalId,
        public string $prn,
        public string $qrMessage,
        public string $terminalName,
        public string $webSocketId,
        public string $location,
        public string $fonePayPanNumber,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            // @phpstan-ignore argument.type
            qrString: strval($data['qrString'] ?? ''),
            // @phpstan-ignore argument.type
            qrDisplayName: strval($data['qrDisplayName'] ?? ''),
            // @phpstan-ignore argument.type
            status: strval($data['status'] ?? ''),
            // @phpstan-ignore argument.type
            terminalId: intval($data['terminalId'] ?? 0),
            // @phpstan-ignore argument.type
            prn: strval($data['prn'] ?? ''),
            // @phpstan-ignore argument.type
            qrMessage: strval($data['qrMessage'] ?? ''),
            // @phpstan-ignore argument.type
            terminalName: strval($data['terminalName'] ?? ''),
            // @phpstan-ignore argument.type
            webSocketId: strval($data['websocketId'] ?? ''),
            // @phpstan-ignore argument.type
            location: strval($data['location'] ?? ''),
            // @phpstan-ignore argument.type
            fonePayPanNumber: strval($data['fonepayPanNumber'] ?? ''),
        );
    }

    /**
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        return [
            'qrString' => $this->qrString,
            'qrDisplayName' => $this->qrDisplayName,
            'status' => $this->status,
            'terminalId' => $this->terminalId,
            'prn' => $this->prn,
            'qrMessage' => $this->qrMessage,
            'terminalName' => $this->terminalName,
            'webSocketId' => $this->webSocketId,
            'location' => $this->location,
            'fonePayPanNumber' => $this->fonePayPanNumber,
        ];
    }
}
