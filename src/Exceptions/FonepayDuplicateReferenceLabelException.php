<?php

declare(strict_types=1);

namespace Mantraideas\LaravelFonepay\Exceptions;

use Exception;

class FonepayDuplicateReferenceLabelException extends Exception
{
    /** @var int */
    protected $code = 409;

    /** @var string */
    protected $message = 'Duplicate reference label';
}
