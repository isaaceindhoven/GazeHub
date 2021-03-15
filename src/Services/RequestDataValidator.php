<?php

/**
  *   Do not remove or alter the notices in this preamble.
  *   This software code regards ISAAC Standard Software.
  *   Copyright © 2021 ISAAC and/or its affiliates.
  *   www.isaac.nl All rights reserved. License grant and user rights and obligations
  *   according to applicable license agreement. Please contact sales@isaac.nl for
  *   questions regarding license and user rights.
  */

declare(strict_types=1);

namespace GazeHub\Services;

use GazeHub\Exceptions\DataValidationFailedException;

use function array_filter;
use function array_key_exists;
use function array_push;
use function count;
use function explode;
use function is_array;
use function is_string;

class RequestDataValidator
{
    /**
     * Validate data using validation rules
     *
     * An example set of validation rules:
     * <code>
     * $checks = [
     *  'username' => 'required|string',
     *  'password' => 'required|string',
     *  'attributes' => 'required|array:string|not_empty',
     * ];
     * </code>
     *
     * @param array         $data       The data to validate
     * @param array         $checks     The validation rules to check
     * @return array                    Validated data
     * @throws DataValidationFailedException    Thrown when the data does not pass all checks
     */
    //phpcs:ignore ObjectCalisthenics.Files.FunctionLength.ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff, Generic.Metrics.CyclomaticComplexity.TooHigh
    public static function validate(array $data, array $checks): array
    {
        $errors = [];

        foreach ($checks as $field => $validatorString) {
            $fieldErrors = [];

            $validators = explode('|', $validatorString);

            foreach ($validators as $validator) {
                $validator = explode(':', $validator);
                $name = $validator[0];
                $key = count($validator) === 1 ? null : $validator[1];

                if ($name === 'required' && !array_key_exists($field, $data)) {
                    array_push($fieldErrors, 'must be present');
                    break;
                }

                if (!array_key_exists($field, $data)) {
                    $data[$field] = null;
                    break;
                }

                $value = $data[$field];

                if ($name === 'string' && !is_string($value)) {
                    array_push($fieldErrors, 'must be a string');
                    break;
                }

                if ($name === 'array' && !is_array($value)) {
                    array_push($fieldErrors, 'must be an array');
                    break;
                }

                if ($name === 'array' && $key !== null && count($value) !== count(array_filter($value, 'is_' . $key))) {
                    array_push($fieldErrors, 'must be array of ' . $key . 's');
                    break;
                }

                if ($name === 'not_empty' && count($value) === 0) {
                    array_push($fieldErrors, 'must not be empty');
                    break;
                }
            }

            if (count($fieldErrors) > 0) {
                $errors[$field] = $fieldErrors;
            }
        }

        if (count($errors) > 0) {
            throw new DataValidationFailedException($errors);
        }

        return $data;
    }
}
