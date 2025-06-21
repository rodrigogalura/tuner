<?php

namespace RGalura\ApiIgniter\Exceptions;

class ProjectionExcludeFieldsException extends \Exception
{
    /**
     * Create a new exception for invalid provided parameter.
     *
     * @param  array  $excludeFields
     */
    public function __construct($excludeFields)
    {
        $message = $this->buildMessage($excludeFields);
        parent::__construct($message);
    }

    /**
     * Build the exception message.
     *
     * @param  array  $excludeFields
     */
    protected function buildMessage($excludeFields): string
    {
        return "Invalid value, cannot used '".implode(',', $excludeFields)."' for excluding fields";
    }
}
