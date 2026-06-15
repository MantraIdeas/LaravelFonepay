<?php

declare(strict_types=1);

use Mantraideas\LaravelFonepay\Exceptions\FonepayDuplicateReferenceLabelException;
use Mantraideas\LaravelFonepay\Exceptions\FonepayException;
use Mantraideas\LaravelFonepay\Exceptions\FonepayInvalidReferenceLabelException;
use Mantraideas\LaravelFonepay\Exceptions\FonepayInvalidTerminalException;
use Mantraideas\LaravelFonepay\Exceptions\FonepayValidationException;
use Mantraideas\LaravelFonepay\Exceptions\InvalidPrivateKeyException;

test('fonepay duplicate reference label exception', function () {
    $exception = new FonepayDuplicateReferenceLabelException;

    expect($exception->getMessage())->toBe('Duplicate reference label');
    expect($exception->getCode())->toBe(409);
});

test('fonepay validation exception without message', function () {
    $exception = new FonepayValidationException;

    expect($exception->getCode())->toBe(422);
});

test('fonepay validation exception with message', function () {
    $exception = new FonepayValidationException('Invalid amount');

    expect($exception->getMessage())->toBe('Invalid amount');
    expect($exception->getCode())->toBe(422);
});

test('fonepay invalid reference label exception', function () {
    $exception = new FonepayInvalidReferenceLabelException;

    expect($exception->getMessage())->toBe('Invalid reference label');
    expect($exception->getCode())->toBe(500);
});

test('fonepay invalid terminal exception', function () {
    $exception = new FonepayInvalidTerminalException;

    expect($exception->getMessage())->toBe('Invalid Terminal Id');
    expect($exception->getCode())->toBe(500);
});

test('fonepay invalid terminal exception with custom message', function () {
    $exception = new FonepayInvalidTerminalException('Custom terminal error');

    expect($exception->getMessage())->toBe('Custom terminal error');
    expect($exception->getCode())->toBe(500);
});

test('fonepay exception is throwable', function () {
    $exception = new FonepayException;

    expect($exception)->toBeInstanceOf(Exception::class);
});

test('fonepay exception with message and code', function () {
    $exception = new FonepayException('API Error', 502);

    expect($exception->getMessage())->toBe('API Error');
    expect($exception->getCode())->toBe(502);
});

test('invalid private key exception', function () {
    $exception = new InvalidPrivateKeyException;

    expect($exception->getMessage())->toBe('Invalid private key');
    expect($exception->getCode())->toBe(500);
});
