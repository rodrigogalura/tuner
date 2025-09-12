<?php

use Tuner\Tuner\V33\ValueObjects\Requests\Request;
use Tuner\Tuner\V33\ValueObjects\Requests\RequestInterface;

it('should throw an exception if the class is not implementation of ['.RequestInterface::class.']', function (): void {
    // Act & Assert
    new class('', []) extends Request
    {
        protected function validate() {}
    };
})->throws(Exception::class);
