<?php

declare(strict_types=1);

namespace Mantraideas\LaravelFonepay\Exceptions;

use Exception;

class FonepayValidationException extends Exception
{
    /** @var int */
    protected $code = 422;
}
