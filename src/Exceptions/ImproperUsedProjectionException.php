<?php

namespace RGalura\ApiIgniter\Exceptions;

class ImproperUsedProjectionException extends \Exception
{
    /**
     * Create a new exception for invalid projected fields.
     *
     * @param  array|string  $fields
     */
    public function __construct($clientFieldsKey = 'fields', $clientFieldsNotKey = 'fields!', $strict = 0)
    {
        parent::__construct("Improper used of projection. Cannot used both parameters '{$clientFieldsKey}' and '{$clientFieldsNotKey}' at the same time.", $strict);
    }
}
