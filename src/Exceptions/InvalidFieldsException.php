<?php

namespace RGalura\ApiIgniter\Exceptions;

class InvalidFieldsException extends \Exception
{
    /**
     * Create a new exception for invalid projected fields.
     *
     * @param  array|string  $fields
     */
    public function __construct($fields)
    {
        $message = $this->buildMessage($fields);
        parent::__construct($message);
    }

    /**
     * Build the exception message.
     *
     * @param  array|string  $fields
     */
    protected function buildMessage($fields): string
    {
        $invalidFields = (array) $fields;

        if (count($invalidFields) === 1) {
            return "The field '{$invalidFields[0]}' is not a valid projectable field.";
        }

        $list = implode("', '", $invalidFields);

        return "The fields '{$list}' are not valid projectable fields.";
    }
}
