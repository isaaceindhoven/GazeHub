<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

use GazeHub\Exceptions\DataValidationFailedException;
use React\Http\Message\Response;

use function array_filter;
use function array_key_exists;
use function array_push;
use function count;
use function explode;
use function is_array;
use function is_string;
use function json_encode;

abstract class BaseController
{
    //phpcs:ignore ObjectCalisthenics.Files.FunctionLength.ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff
    protected function validatedData(array $data, array $checks): array
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

    private function end(string $text, array $headers, int $statusCode): Response
    {
        return new Response($statusCode, $headers, $text);
    }

    protected function json(array $data, int $statusCode = 200): Response
    {
        return $this->end(json_encode($data), [ 'Content-Type' => 'application/json' ], $statusCode);
    }

    protected function html(string $html, int $statusCode = 200): Response
    {
        return $this->end($html, [ 'Content-Type' => 'text/html' ], $statusCode);
    }
}
