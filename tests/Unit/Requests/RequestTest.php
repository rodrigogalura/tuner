<?php

use Tuner\Requests\Request;
use Tuner\Requests\RequestInterface;

it('should throw an exception if the class is not implementation of ['.RequestInterface::class.']', function (): void {
    // Act & Assert
    new class('', []) extends Request
    {
        protected function validate() {}
    };
})->throws(Exception::class);
