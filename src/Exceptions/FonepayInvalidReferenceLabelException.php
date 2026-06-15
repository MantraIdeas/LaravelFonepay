<?php

declare(strict_types=1);

namespace Mantraideas\LaravelFonepay\Exceptions;

use Exception;

class FonepayInvalidReferenceLabelException extends Exception
{
    /** @var int */
    protected $code = 500;

    /** @var string */
    protected $message = 'Invalid reference label';
}
