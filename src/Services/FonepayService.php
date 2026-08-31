<?php

declare(strict_types=1);

namespace Mantraideas\LaravelFonepay\Services;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Mantraideas\LaravelFonepay\DTOs\BankDTO;
use Mantraideas\LaravelFonepay\DTOs\GenerateQrCodeDTO;
use Mantraideas\LaravelFonepay\DTOs\QrCodeDTO;
use Mantraideas\LaravelFonepay\DTOs\StatusDTO;
use Mantraideas\LaravelFonepay\DTOs\TokenDTO;
use Mantraideas\LaravelFonepay\Exceptions\FonepayDuplicateReferenceLabelException;
use Mantraideas\LaravelFonepay\Exceptions\FonepayException;
use Mantraideas\LaravelFonepay\Exceptions\FonepayInvalidReferenceLabelException;
use Mantraideas\LaravelFonepay\Exceptions\FonepayInvalidTerminalException;
use Mantraideas\LaravelFonepay\Exceptions\FonepayValidationException;
use Mantraideas\LaravelFonepay\Exceptions\InvalidPrivateKeyException;

class FonepayService
{
    /**
     * @throws InvalidPrivateKeyException
     * @throws ConnectionException
     * @throws FonepayException
     * @throws FonepayDuplicateReferenceLabelException
     * @throws FonepayValidationException
     * @throws Exception
     */
    public function generateQrCode(GenerateQrCodeDTO $data): QrCodeDTO
    {
        $token = $this->getOAuthToken();

        $url = $this->baseUrl().$this->basePath().'/generate-intent-qr';

        $signature = $this->generateSignature(json_encode($data->toArray(), JSON_THROW_ON_ERROR));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $token->accessToken,
            'Signature' => $signature,
        ]);

        $response = $response->post($url, $data->toArray());

        $statusCode = $response->getStatusCode();
        if ($statusCode === 409) {
            throw new FonepayDuplicateReferenceLabelException;
        } elseif ($statusCode === 400) {
            $body = $response->json();
            assert(is_array($body));
            $message = json_encode($body['message']);
            throw new FonepayValidationException((string) $message);
        }
        if ($response->failed()) {
            $body = $response->json();
            assert(is_array($body));
            /** @var array<string, mixed> $body */
            $message = $body['message'] ?? '';

            throw new FonepayException(is_string($message) ? $message : (json_encode($message) ?: ''),
                $response->getStatusCode());
        }
        $body = $response->json();
        assert(is_array($body));

        /** @var array<string, mixed> $body */
        return QrCodeDTO::fromArray($body);
    }

    /**
     * @throws Exception
     */
    private function getOAuthToken(): TokenDTO
    {
        $url = $this->baseUrl().$this->basePath().'/login';
        // @phpstan-ignore cast.string
        $username = (string) config('fonepay.username');
        // @phpstan-ignore cast.string
        $password = (string) config('fonepay.password');
        $data = [
            'username' => $username,
            'password' => $password,
        ];

        $signature = $this->generateSignature(json_encode($data, JSON_THROW_ON_ERROR));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.base64_encode($username.':'.$password),
            'Signature' => $signature,
        ]);

        $response = $response->post($url, $data);

        if ($response->failed()) {
            $body = $response->json();
            $message = is_array($body) && isset($body['error']) && is_string($body['error'])
                ? $body['error']
                : 'Authentication failed';

            throw new FonepayException($message, $response->getStatusCode());
        }

        $body = $response->json();
        assert(is_array($body));

        /** @var array<string, mixed> $body */
        return TokenDTO::fromArray($body);
    }

    private function baseUrl(): string
    {
        // @phpstan-ignore cast.string
        return (string) config('fonepay.base_url');
    }

    private function basePath(): string
    {
        // @phpstan-ignore cast.string
        return (string) config('fonepay.base_path');
    }

    /**
     * @throws InvalidPrivateKeyException
     */
    private function generateSignature(string $rawBody): string
    {
        // @phpstan-ignore cast.string
        $keyPath = (string) config('fonepay.private_key_path');
        $privateKey = file_get_contents($keyPath);

        if ($privateKey === false) {
            throw new InvalidPrivateKeyException;
        }

        $key = openssl_pkey_get_private($privateKey);

        if ($key === false) {
            throw new InvalidPrivateKeyException;
        }

        $signature = '';
        openssl_sign($rawBody, $signature, $key, OPENSSL_ALGO_SHA256);

        // @phpstan-ignore argument.type
        return base64_encode($signature);
    }

    /**
     * @return array<BankDTO>
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function getBanks(): array
    {
        $token = $this->getOAuthToken();
        $url = $this->baseUrl().$this->basePath().'/banks/list';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $token->accessToken,
            'paymentMode' => 'INTENT',
            'signature' => $this->generateSignature(json_encode([], JSON_THROW_ON_ERROR)),
        ]);

        $response = $response->get($url);
        $body = $response->json();
        assert(is_array($body));
        /** @var array<string, mixed> $body */
        $bankDetails = $body['bankDetails'] ?? [];
        assert(is_array($bankDetails));
        $data = [];
        foreach ($bankDetails as $bank) {
            assert(is_array($bank));
            /** @var array<string, mixed> $bank */
            $data[] = BankDTO::fromArray($bank);
        }

        return $data;
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function getPaymentStatus(string $referenceLabel): StatusDTO
    {
        $token = $this->getOAuthToken();
        $url = $this->baseUrl().$this->basePath().'/thirdPartyDynamicQrGetStatus';
        $data = [
            // @phpstan-ignore cast.string
            'terminalId' => (string) config('fonepay.terminal_id'),
            'referenceLabel' => $referenceLabel,
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $token->accessToken,
            'Signature' => $this->generateSignature(json_encode($data, JSON_THROW_ON_ERROR)),
        ]);

        $response = $response->post($url, $data);

        $statusCode = $response->getStatusCode();

        if ($statusCode === 409) {
            $body = $response->json();
            assert(is_array($body));
            /** @var array<string, mixed> $body */
            $message = $body['message'] ?? '';

            throw new FonepayInvalidTerminalException(
                is_string($message)
                    ? $message
                    : (json_encode($message) ?: '')
            );
        } elseif ($statusCode === 500) {
            throw new FonepayInvalidReferenceLabelException;
        } elseif ($response->failed()) {
            $body = $response->json();
            assert(is_array($body));
            /** @var array<string, mixed> $body */
            $error = $body['error'] ?? '';

            throw new FonepayException(
                is_string($error)
                    ? $error
                    : (json_encode($error) ?: ''),
                $response->getStatusCode()
            );
        }
        $body = $response->json();
        assert(is_array($body));

        /** @var array<string, mixed> $body */
        return StatusDTO::fromArray($body);
    }
}
