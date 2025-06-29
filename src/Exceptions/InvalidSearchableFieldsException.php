<?php

namespace RGalura\ApiIgniter\Exceptions;

class InvalidSearchableFieldsException extends \Exception
{
    public function __construct(private array $invalidFields, int $strict = 1)
    {
        parent::__construct($this->buildMessage(), $strict);
    }

    /**
     * Build the exception message.
     */
    protected function buildMessage(): string
    {
        $fields = array_values($this->invalidFields);

        if (count($fields) === 1) {
            return "The field '{$fields[0]}' is not a valid field.";
        }

        $list = implode("', '", $fields);

        return "The fields '{$list}' are not valid fields.";
    }
}
