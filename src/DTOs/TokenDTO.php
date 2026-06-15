<?php

declare(strict_types=1);

namespace Mantraideas\LaravelFonepay\DTOs;

class TokenDTO
{
    public function __construct(
        public string $username,
        public string $accessToken,
        public string $refreshToken,
        public string $tokenType,
        public int $expiresIn,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            // @phpstan-ignore argument.type
            username: strval($data['username'] ?? ''),
            // @phpstan-ignore argument.type
            accessToken: strval($data['accessToken'] ?? ''),
            // @phpstan-ignore argument.type
            refreshToken: strval($data['refreshToken'] ?? ''),
            // @phpstan-ignore argument.type
            tokenType: strval($data['tokenType'] ?? ''),
            // @phpstan-ignore argument.type
            expiresIn: intval($data['expiresIn'] ?? 0),
        );
    }
}
