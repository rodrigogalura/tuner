<?php

namespace RGalura\ApiIgniter\Exceptions;

class InvalidFieldsException extends \Exception
{
    /**
     * Create a new exception for invalid fields.
     *
     * @param  array|string  $fields
     */
    public function __construct(array $fields)
    {
        $message = $this->buildMessage($fields);
        parent::__construct($message);
    }

    /**
     * Build the exception message.
     *
     * @param  array|string  $fields
     */
    protected function buildMessage(array $fields): string
    {
        $invalidFields = array_values($fields);

        if (count($invalidFields) === 1) {
            return "The field '{$invalidFields[0]}' is not a valid field.";
        }

        $list = implode("', '", $invalidFields);

        return "The fields '{$list}' are not valid fields.";
    }
}
