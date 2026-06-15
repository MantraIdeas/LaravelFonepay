<?php

declare(strict_types=1);

namespace Mantraideas\LaravelFonepay\Facades;

use Illuminate\Support\Facades\Facade;
use Mantraideas\LaravelFonepay\DTOs\BankDTO;
use Mantraideas\LaravelFonepay\DTOs\GenerateQrCodeDTO;
use Mantraideas\LaravelFonepay\DTOs\QrCodeDTO;
use Mantraideas\LaravelFonepay\DTOs\StatusDTO;
use Mantraideas\LaravelFonepay\Services\FonepayService;

/**
 * @method static QrCodeDTO generateQrCode(GenerateQrCodeDTO $data)
 * @method static array<BankDTO> getBanks()
 * @method static StatusDTO getPaymentStatus(string $referenceLabel)
 */
class Fonepay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FonepayService::class;
    }
}
