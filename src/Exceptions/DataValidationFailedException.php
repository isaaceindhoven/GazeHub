<?php

declare(strict_types=1);

namespace GazeHub\Exceptions;

use Exception;

class DataValidationFailedException extends Exception
{
    /**
     * @var array
     */
    public $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }
}
