![Laravel FonePay](https://banners.beyondco.de/Laravel%20FonePay.png?theme=light&packageManager=composer+require&packageName=mantraideas%2Flaravel-fonepay&pattern=architect&style=style_1&description=Integrate+FonePay+payment+gateway+into+your+Laravel+application&md=1&showWatermark=1&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

# Laravel FonePay

[![Latest Stable Version](http://poser.pugx.org/mantraideas/laravel-fonepay/v)](https://packagist.org/packages/mantraideas/laravel-fonepay)
[![Total Downloads](http://poser.pugx.org/mantraideas/laravel-fonepay/downloads)](https://packagist.org/packages/mantraideas/laravel-fonepay)
[![License](http://poser.pugx.org/mantraideas/laravel-fonepay/license)](https://packagist.org/packages/mantraideas/laravel-fonepay)

The `mantraideas/laravel-fonepay` package allows you to integrate the FonePay payment gateway into your Laravel application. It supports QR code payment generation, bank listing, and payment status verification.

## Requirements

- PHP ^8.1
- Laravel ^13.0

## Installation

```bash
composer require mantraideas/laravel-fonepay
```

## Configuration

### Publish Config File

```bash
php artisan vendor:publish --provider="Mantraideas\LaravelFonepay\LaravelFonepayServiceProvider"
```

This will create a `config/fonepay.php` file in your Laravel application.

### Environment Variables

Set the following in your `.env` file:

```dotenv
FONEPAY_USERNAME="your_merchant_username"
FONEPAY_PASSWORD="your_merchant_password"
FONEPAY_BASE_URL="https://dev-external-gateway-new.fonepay.com/merchantThirdparty"
FONEPAY_BASE_PATH="/api/merchant/third-party/v2"
FONEPAY_TERMINAL_ID="your_terminal_id"
```

| Variable | Description |
|---|---|
| `FONEPAY_USERNAME` | Your FonePay merchant username |
| `FONEPAY_PASSWORD` | Your FonePay merchant password |
| `FONEPAY_BASE_URL` | FonePay API base URL (defaults to development endpoint) |
| `FONEPAY_BASE_PATH` | FonePay API base path |
| `FONEPAY_TERMINAL_ID` | Your FonePay terminal ID |
| `FONEPAY_PRIVATE_KEY_PATH` | Path to your RSA private key file (defaults to `storage_path('keys/private.pem')`) |

### Private Key

Place your RSA private key file in `storage/keys/private.pem` (or configure the path via `FONEPAY_PRIVATE_KEY_PATH`). This key is used to sign API requests.

## Usage

### Generate QR Code for Payment

```php
use Mantraideas\LaravelFonepay\DTOs\GenerateQrCodeDTO;
use Mantraideas\LaravelFonepay\Facades\Fonepay;

$qrCode = Fonepay::generateQrCode(new GenerateQrCodeDTO(
    amount: 1000.00,
    billId: 'BILL-' . uniqid(),
    terminalId: config('fonepay.terminal_id'),
    paymentMode: 'QR',
    referenceLabel: 'REF-' . uniqid(),
    qrType: 'INTENT_QR',
));

// Response contains:
$qrCode->qrString;       // QR string data
$qrCode->prn;            // Payment reference number
$qrCode->status;         // Status of QR generation
$qrCode->qrDisplayName;  // Display name for the QR
```

> Note: You need to follow guidelines from FonePay to show the QR code to the customer.

#### GenerateQrCodeDTO Parameters

| Parameter | Type | Description |
|---|---|---|
| `amount` | `float` | Payment amount |
| `billId` | `string` | Unique bill identifier |
| `terminalId` | `string` | Your FonePay terminal ID |
| `paymentMode` | `string` | Payment mode (e.g. `'QR'`) |
| `referenceLabel` | `string` | Unique reference label for this transaction |
| `qrType` | `string` | QR type (e.g. `'INTENT_QR'`) |

### Get Payment Status

```php
use Mantraideas\LaravelFonepay\Facades\Fonepay;

$status = Fonepay::getPaymentStatus('PRN-001');

// Response:
$status->referenceLabel;          // The reference label (PRN)
$status->merchantCode;           // Merchant code
$status->paymentStatus;          // Payment status (e.g. 'COMPLETED')
$status->requestedAmount;        // Requested amount
$status->totalTransactionAmount; // Total transaction amount
$status->paymentMessage;         // Payment message
$status->fonepayTraceId;         // FonePay trace ID (optional)
```

### List Available Banks

```php
use Mantraideas\LaravelFonepay\Facades\Fonepay;

$banks = Fonepay::getBanks();

foreach ($banks as $bank) {
    $bank->bankName;      // Bank name
    $bank->bankCode;      // Bank code
    $bank->bankIcon;      // Bank icon URL
    $bank->packageName;   // Android package name
    $bank->intentScheme;  // Android intent scheme
}
```

### Using the Facade

The `Fonepay` facade is auto-registered by the service provider, so you can use it directly without manually resolving the service:

```php
use Mantraideas\LaravelFonepay\Facades\Fonepay;

// Generate QR Code
$qrCode = Fonepay::generateQrCode($dto);

// List Banks
$banks = Fonepay::getBanks();

// Check Payment Status
$status = Fonepay::getPaymentStatus('PRN-001');
```

## DTO Reference

### `GenerateQrCodeDTO` (Input)

Used with `Fonepay::generateQrCode()`. Construct using named arguments:

```php
new GenerateQrCodeDTO(
    amount: float,         // Payment amount
    billId: string,        // Unique bill identifier
    terminalId: string,    // Your FonePay terminal ID
    paymentMode: string,   // e.g. 'QR'
    referenceLabel: string,// Unique reference label per transaction
    qrType: string,        // e.g. 'INTENT_QR'
);
```

| Property | Type | Description |
|---|---|---|
| `amount` | `float` | Payment amount |
| `billId` | `string` | Unique bill identifier |
| `terminalId` | `string` | Your FonePay terminal ID |
| `paymentMode` | `string` | Payment mode (e.g. `'QR'`) |
| `referenceLabel` | `string` | Unique reference label for this transaction |
| `qrType` | `string` | QR type (e.g. `'INTENT_QR'`) |

---

### `QrCodeDTO` (Return)

Returned by `Fonepay::generateQrCode()`:

| Property | Type | Description |
|---|---|---|
| `qrString` | `string` | QR string data |
| `qrDisplayName` | `string` | Display name for the QR |
| `status` | `string` | Status of QR generation |
| `terminalId` | `int` | Terminal ID |
| `prn` | `string` | Payment reference number |
| `qrMessage` | `string` | QR message |
| `terminalName` | `string` | Terminal name |
| `webSocketId` | `string` | WebSocket ID |
| `location` | `string` | Location |
| `fonePayPanNumber` | `string` | FonePay PAN number |

---

### `StatusDTO` (Return)

Returned by `Fonepay::getPaymentStatus()`:

| Property | Type | Description |
|---|---|---|
| `referenceLabel` | `string` | The reference label (PRN) |
| `merchantCode` | `string` | Merchant code |
| `paymentStatus` | `string` | Payment status (e.g. `'COMPLETED'`) |
| `requestedAmount` | `string` | Requested amount |
| `totalTransactionAmount` | `string` | Total transaction amount |
| `paymentMessage` | `string` | Payment message |
| `fonepayTraceId` | `?int` | FonePay trace ID (nullable) |

---

### `BankDTO` (Return)

Returned by `Fonepay::getBanks()`:

| Property | Type | Description |
|---|---|---|
| `bankName` | `string` | Bank name |
| `bankCode` | `string` | Bank code |
| `bankIcon` | `string` | Bank icon URL |
| `packageName` | `string` | Android package name |
| `intentScheme` | `string` | Android intent scheme |

## Exceptions

| Exception | Condition |
|---|---|
| `FonepayDuplicateReferenceLabelException` | Duplicate reference label (HTTP 409) |
| `FonepayValidationException` | Validation error (HTTP 400) |
| `FonepayInvalidTerminalException` | Invalid terminal ID (HTTP 409 on status check) |
| `FonepayInvalidReferenceLabelException` | Invalid reference label (HTTP 500 on status check) |
| `FonepayException` | Generic API error |
| `InvalidPrivateKeyException` | Missing or invalid private key |

## Testing

```bash
composer test
```

## License

[MIT](LICENSE)

## Author

- [@Dipesh79](https://github.com/Dipesh79)

## Support

For support, email [dipeshkhanal79@gmail.com](mailto:dipeshkhanal79@gmail.com).
