<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Http;
use Mantraideas\LaravelFonepay\DTOs\GenerateQrCodeDTO;
use Mantraideas\LaravelFonepay\DTOs\QrCodeDTO;
use Mantraideas\LaravelFonepay\DTOs\StatusDTO;
use Mantraideas\LaravelFonepay\Exceptions\FonepayDuplicateReferenceLabelException;
use Mantraideas\LaravelFonepay\Exceptions\FonepayException;
use Mantraideas\LaravelFonepay\Exceptions\FonepayInvalidReferenceLabelException;
use Mantraideas\LaravelFonepay\Exceptions\FonepayInvalidTerminalException;
use Mantraideas\LaravelFonepay\Exceptions\FonepayValidationException;
use Mantraideas\LaravelFonepay\Exceptions\InvalidPrivateKeyException;
use Mantraideas\LaravelFonepay\Services\FonepayService;

function makeLoginResponse(): array
{
    return [
        'username' => 'test_user',
        'accessToken' => 'token',
        'refreshToken' => 'refresh',
        'tokenType' => 'Bearer',
        'expiresIn' => 3600,
    ];
}

function makeQrResponse(): array
{
    return [
        'qrString' => 'qr-string',
        'qrDisplayName' => 'Test',
        'status' => 'SUCCESS',
        'terminalId' => 123456,
        'prn' => 'PRN-001',
        'qrMessage' => 'QR generated',
        'terminalName' => 'Terminal',
        'websocketId' => 'WS-001',
        'location' => 'Kathmandu',
        'fonepayPanNumber' => 'PAN-001',
    ];
}

function makeBasicDto(): GenerateQrCodeDTO
{
    return new GenerateQrCodeDTO(
        amount: 100.00,
        billId: 'BILL-001',
        terminalId: 'TERM-001',
        paymentMode: 'QR',
        referenceLabel: 'REF-001',
        qrType: 'INTENT_QR',
    );
}

beforeEach(function () {
    $app = new Application;
    $app->instance('config', new Repository);
    Facade::setFacadeApplication($app);

    $config = $app->make('config');
    $config->set('fonepay', [
        'username' => 'test_user',
        'password' => 'test_pass',
        'base_url' => 'https://api.fonepay.test',
        'base_path' => '/api/v2',
        'terminal_id' => 'TEST_TERMINAL',
        'private_key_path' => __DIR__.'/../../test.key',
    ]);

    $keyResource = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($keyResource, $privateKey);
    file_put_contents(__DIR__.'/../../test.key', $privateKey);

    $this->service = new FonepayService;
});

afterEach(function () {
    $keyPath = __DIR__.'/../../test.key';
    if (file_exists($keyPath)) {
        unlink($keyPath);
    }

    Facade::clearResolvedInstances();
    Facade::setFacadeApplication(null);
});

test('generate qr code sends request and returns dto', function () {
    Http::fake([
        'https://api.fonepay.test/api/v2/login' => Http::response(makeLoginResponse()),
        'https://api.fonepay.test/api/v2/generate-intent-qr' => Http::response(makeQrResponse()),
    ]);

    $result = $this->service->generateQrCode(makeBasicDto());

    expect($result)->toBeInstanceOf(QrCodeDTO::class);
    expect($result->qrString)->toBe('qr-string');
    expect($result->prn)->toBe('PRN-001');
});

test('generate qr code throws duplicate reference exception on 409', function () {
    Http::fakeSequence()
        ->push(makeLoginResponse(), 200)
        ->push(['message' => 'Duplicate'], 409);

    expect(fn () => $this->service->generateQrCode(makeBasicDto()))
        ->toThrow(FonepayDuplicateReferenceLabelException::class);
});

test('generate qr code throws validation exception on 400', function () {
    Http::fakeSequence()
        ->push(makeLoginResponse(), 200)
        ->push(['message' => 'Invalid'], 400);

    expect(fn () => $this->service->generateQrCode(makeBasicDto()))
        ->toThrow(FonepayValidationException::class);
});

test('generate qr code throws generic exception on other failure', function () {
    Http::fakeSequence()
        ->push(makeLoginResponse(), 200)
        ->push(['message' => 'Error'], 500);

    expect(fn () => $this->service->generateQrCode(makeBasicDto()))
        ->toThrow(FonepayException::class);
});

test('get banks returns array of bank dto', function () {
    Http::fake([
        'https://api.fonepay.test/api/v2/login' => Http::response(makeLoginResponse()),
        'https://api.fonepay.test/api/v2/banks/list' => Http::response([
            'bankDetails' => [
                ['bankName' => 'Nepal Bank', 'bankCode' => 'NBL', 'bankIcon' => 'a', 'packageName' => 'b', 'intentScheme' => 'c'],
                ['bankName' => 'Himalayan Bank', 'bankCode' => 'HBL', 'bankIcon' => 'd', 'packageName' => 'e', 'intentScheme' => 'f'],
            ],
        ]),
    ]);

    $banks = $this->service->getBanks();

    expect($banks)->toHaveCount(2);
    expect($banks[0]->bankName)->toBe('Nepal Bank');
    expect($banks[1]->bankName)->toBe('Himalayan Bank');
});

test('get payment status returns status dto', function () {
    Http::fake([
        'https://api.fonepay.test/api/v2/login' => Http::response(makeLoginResponse()),
        'https://api.fonepay.test/api/v2/thirdPartyDynamicQrGetStatus' => Http::response([
            'prn' => 'PRN-001', 'merchantCode' => 'M1', 'paymentStatus' => 'COMPLETED',
            'fonepayTraceId' => 12345, 'requestedAmount' => '100', 'totalTransactionAmount' => '100',
            'paymentMessage' => 'OK',
        ]),
    ]);

    $status = $this->service->getPaymentStatus('PRN-001');

    expect($status)->toBeInstanceOf(StatusDTO::class);
    expect($status->paymentStatus)->toBe('COMPLETED');
});

test('get payment status throws terminal exception on 409', function () {
    Http::fakeSequence()
        ->push(makeLoginResponse(), 200)
        ->push(['message' => 'Bad'], 409);

    expect(fn () => $this->service->getPaymentStatus('PRN-001'))
        ->toThrow(FonepayInvalidTerminalException::class);
});

test('get payment status throws invalid reference exception on 500', function () {
    Http::fakeSequence()
        ->push(makeLoginResponse(), 200)
        ->push([], 500);

    expect(fn () => $this->service->getPaymentStatus('PRN-001'))
        ->toThrow(FonepayInvalidReferenceLabelException::class);
});

test('get payment status throws generic exception on other failure', function () {
    Http::fakeSequence()
        ->push(makeLoginResponse(), 200)
        ->push(['error' => 'Err'], 503);

    expect(fn () => $this->service->getPaymentStatus('PRN-001'))
        ->toThrow(FonepayException::class);
});

test('generate qr code sends signature header', function () {
    Http::fake([
        'https://api.fonepay.test/api/v2/login' => Http::response(makeLoginResponse()),
        'https://api.fonepay.test/api/v2/generate-intent-qr' => Http::response(makeQrResponse()),
    ]);

    $this->service->generateQrCode(makeBasicDto());

    Http::assertSent(fn (Request $request) => $request->hasHeader('Signature'));
});

test('throws invalid private key exception when key file is missing', function () {
    $config = app('config');
    $config->set('fonepay.private_key_path', '/nonexistent/key.pem');

    $this->service = new FonepayService;

    expect(fn () => $this->service->generateQrCode(makeBasicDto()))
        ->toThrow(InvalidPrivateKeyException::class);
});

test('throws invalid private key exception when key content is invalid', function () {
    $config = app('config');
    $config->set('fonepay.private_key_path', __DIR__.'/../../test.key');
    file_put_contents(__DIR__.'/../../test.key', 'not a valid private key');

    $this->service = new FonepayService;

    expect(fn () => $this->service->generateQrCode(makeBasicDto()))
        ->toThrow(InvalidPrivateKeyException::class);
});

test('service adds authorization header to requests', function () {
    Http::fake([
        'https://api.fonepay.test/api/v2/login' => Http::response(makeLoginResponse()),
        'https://api.fonepay.test/api/v2/generate-intent-qr' => Http::response(makeQrResponse()),
    ]);

    $this->service->generateQrCode(makeBasicDto());

    Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization'));
});

test('throws authentication exception when login fails with error message', function () {
    Http::fake([
        'https://api.fonepay.test/api/v2/login' => Http::response(['message' => 'Invalid credentials'], 401),
    ]);

    expect(fn () => $this->service->generateQrCode(makeBasicDto()))
        ->toThrow(FonepayException::class, 'Invalid credentials');
});

test('throws authentication exception when login fails without error message', function () {
    Http::fake([
        'https://api.fonepay.test/api/v2/login' => Http::response([], 401),
    ]);

    expect(fn () => $this->service->generateQrCode(makeBasicDto()))
        ->toThrow(FonepayException::class, 'Authentication failed');
});
