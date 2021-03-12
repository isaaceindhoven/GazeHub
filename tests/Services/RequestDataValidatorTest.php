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

namespace GazeHub\Tests\Services;

use GazeHub\Exceptions\DataValidationFailedException;
use GazeHub\Services\RequestDataValidator;
use PHPUnit\Framework\TestCase;

class RequestDataValidatorTest extends TestCase
{
    public function testShouldThrowIfRequiredFieldIsNotPresent()
    {
        // Arrange
        $this->expectException(DataValidationFailedException::class);
        $data = [];
        $checks = ['field' => 'required'];

        // Act
        RequestDataValidator::validate($data, $checks);
    }

    public function testShouldThrowIfStringFieldIsNotAString()
    {
        // Arrange
        $this->expectException(DataValidationFailedException::class);
        $data = ['field' => 1];
        $checks = ['field' => 'string'];

        // Act
        RequestDataValidator::validate($data, $checks);
    }

    public function testShouldThrowIfArrayFieldIsNotAnArray()
    {
        // Arrange
        $this->expectException(DataValidationFailedException::class);
        $data = ['field' => 'not an array'];
        $checks = ['field' => 'array'];

        // Act
        RequestDataValidator::validate($data, $checks);
    }

    public function testShouldThrowIfStringArrayDoesNotOnlyContainsStrings()
    {
        // Arrange
        $this->expectException(DataValidationFailedException::class);
        $data = ['field' => ['string array', 'with', 1]];
        $checks = ['field' => 'array:string'];

        // Act
        RequestDataValidator::validate($data, $checks);
    }

    public function testShouldThrowIfFieldMustNotBeEmpty()
    {
        // Arrange
        $this->expectException(DataValidationFailedException::class);
        $data = ['field' => []];
        $checks = ['field' => 'array|not_empty'];

        // Act
        RequestDataValidator::validate($data, $checks);
    }

    public function testShouldThrowIfOneValidationFailsWhenMultipleAreApplied()
    {
        // Arrange
        $this->expectException(DataValidationFailedException::class);
        $data = ['field' => [1,2,3,4,5]];
        $checks = ['field' => 'required|array:string'];

        // Act
        RequestDataValidator::validate($data, $checks);
    }

    public function testShouldNotThrowIfDataIsValid()
    {
        // Arrange
        $data = [
            'field' => 'this is a string',
            'array_field' => [],
            'not_empty_array' => [1],
            'empty_string_array' => [],
        ];
        $checks = [
            'field' => 'required|string',
            'array_field' => 'required|array',
            'not_empty_array' => 'required|array|not_empty',
            'empty_string_array' => 'required|array:string',
            'not_required' => 'string',
        ];

        // Act
        $result = RequestDataValidator::validate($data, $checks);

        // Assert
        $this->assertEquals($data, $result);
    }
}
