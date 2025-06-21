<?php

namespace RGalura\ApiIgniter\Exceptions;

class ImproperUsedProjectionException extends \Exception
{
    /**
     * Create a new exception for invalid projected fields.
     *
     * @param  array|string  $fields
     */
    public function __construct($clientFieldsKey, $clientFieldsNotKey)
    {
        $message = "Improper used of projection. Cannot used both parameters '{$clientFieldsKey}' and '{$clientFieldsNotKey}' at the same time.";
        parent::__construct($message);
    }
}
