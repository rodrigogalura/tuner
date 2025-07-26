<?php

namespace Laradigs\Tweaker\Projection;

use Laradigs\Tweaker\DisabledException;
use Laradigs\Tweaker\Projection\Exceptions\CannotUseMultipleProjectionException;
use Laradigs\Tweaker\Projection\Exceptions\DefinedFieldsAreEmptyException;
use Laradigs\Tweaker\Projection\Exceptions\InvalidDefinedFieldsException;
use Laradigs\Tweaker\Projection\Exceptions\InvalidProjectableException;
use Laradigs\Tweaker\TruthTable;

use function RGalura\ApiIgniter\validate;

abstract class Projection
{
    protected TruthTable $truthTable;

    protected readonly string $key;

    protected readonly mixed $clientInputValue;

    public static $clientInputs = [];

    public function __construct(
        protected array $visibleFields,
        protected mixed $projectableFields,
        protected mixed $definedFields,
        private array $clientInput,
    ) {
        $this->truthTable = new TruthTable($visibleFields);

        $this->key = key($clientInput);
        $this->clientInputValue = static::$clientInputs[$this->key] = current($clientInput);
    }

    private function throwIfNotInVisibleFields(array $fields, $exception)
    {
        throw_if($diff = $this->truthTable->diffFromAllItems($fields), new $exception($diff));
    }

    protected function prerequisites()
    {
        // Projectable
        throw_if(empty($this->projectableFields), DisabledException::class);

        $this->truthTable->extractIfAsterisk($this->projectableFields);
        $this->throwIfNotInVisibleFields($this->projectableFields, InvalidProjectableException::class);
        $this->truthTable->intersectToAllItems($this->projectableFields);

        // Defined
        throw_if(empty($this->definedFields), DefinedFieldsAreEmptyException::class);

        $this->truthTable->extractIfAsterisk($this->definedFields);
        $this->throwIfNotInVisibleFields($this->definedFields, InvalidDefinedFieldsException::class);

        $this->projectableFields = $this->truthTable->intersect($this->projectableFields, $this->definedFields);
        throw_if(empty($this->projectableFields), InvalidDefinedFieldsException::class, $this->definedFields);
    }

    protected function validate()
    {
        validate($this->clientInput, 'string');
    }

    public static function getKeyCanUse()
    {
        $keys = array_keys(
            array_filter(static::$clientInputs) // remove all falsy value
        );

        throw_if(count($keys) > 1, CannotUseMultipleProjectionException::class, $keys);

        return count($keys) === 1 ? $keys[0] : null;
    }

    public static function clearKeys()
    {
        static::$clientInputs = [];
    }

    abstract public function project();
}
