<?php

namespace Laradigs\Tweaker\Projection;

use Laradigs\Tweaker\TruthTable;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\NoDefinedFieldException;

abstract class Projection
{
    protected TruthTable $truthTable;

    protected readonly mixed $clientInputValue;

    public static $clientInputs = [];

    public function __construct(
        array $columnListing,
        protected array $projectableFields,
        protected array $definedFields,
        array $clientInput,
    ) {
        $this->truthTable = new TruthTable($columnListing);

        $this->clientInputValue = static::$clientInputs[key($clientInput)] = current($clientInput);
    }

    private function throwIfNotInVisibleFields(array $fields)
    {
        throw_if($diff = $this->truthTable->diffFromAllItems($fields), InvalidFieldsException::class, $diff);
    }

    protected function prerequisites()
    {
        // Make sure client input type is string
        throw_if(! is_string($this->clientInputValue), NoActionWillPerformException::class);
    }

    protected function validate()
    {
        throw_if(empty($this->projectableFields), NoActionWillPerformException::class);

        $this->truthTable->extractIfAsterisk($this->projectableFields);
        $this->throwIfNotInVisibleFields($this->projectableFields);

        throw_if(empty($this->definedFields), NoDefinedFieldException::class);

        $this->truthTable->extractIfAsterisk($this->definedFields);
        $this->throwIfNotInVisibleFields($this->definedFields);

        $this->projectableFields = $this->truthTable->intersect($this->projectableFields, $this->definedFields);
        throw_if(empty($this->projectableFields), NoActionWillPerformException::class);
    }

    public static function getKeyCanUse()
    {
        // remove all falsy value
        $clientInput = array_filter(static::$clientInputs);

        // check if there is one truthy value remains
        return count($clientInput) === 1 ? key($clientInput) : null;
    }

    public static function clearKeys()
    {
        static::$clientInputs = [];
    }

    abstract public function project();
}
