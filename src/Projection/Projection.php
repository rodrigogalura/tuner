<?php

namespace Laradigs\Tweaker\Projection;

use function RGalura\ApiIgniter\abc;
use Laradigs\Tweaker\TruthTable;
use Laradigs\Tweaker\InvalidClientInput;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laradigs\Tweaker\Projection\Exceptions\ProjectionIsEmptyException;
use Laradigs\Tweaker\Projection\Exceptions\InvalidProjectableException;
use Laradigs\Tweaker\Projection\Exceptions\InvalidDefinedFieldsException;
use Laradigs\Tweaker\Projection\Exceptions\DefinedFieldsAreEmptyException;
use Laradigs\Tweaker\Projection\Exceptions\CannotUseMultipleProjectionException;

abstract class Projection
{
    protected TruthTable $truthTable;

    protected readonly string $key;
    protected readonly mixed $clientInputValue;

    public static $clientInputs = [];

    public function __construct(
        array $visibleFields,
        protected array $projectableFields,
        protected array $definedFields,
        private array $clientInput,
    ) {
        $this->truthTable = new TruthTable($visibleFields);

        $this->key = key($clientInput);
        $this->clientInputValue = static::$clientInputs[$this->key] = current($clientInput);
    }

    private function throwIfNotInVisibleFields(array $fields, $exception)
    {
        throw_if($diff = $this->truthTable->diffFromAllItems($fields), $exception, $diff);
    }

    protected function prerequisites()
    {
        # Projectable
        throw_if(empty($this->projectableFields), ProjectionIsEmptyException::class);

        $this->truthTable->extractIfAsterisk($this->projectableFields);
        $this->throwIfNotInVisibleFields($this->projectableFields, InvalidProjectableException::class);

        # Defined
        throw_if(empty($this->definedFields), DefinedFieldsAreEmptyException::class);

        $this->truthTable->extractIfAsterisk($this->definedFields);
        $this->throwIfNotInVisibleFields($this->definedFields, InvalidDefinedFieldsException::class);

        $this->projectableFields = $this->truthTable->intersect($this->projectableFields, $this->definedFields);
        throw_if(empty($this->projectableFields), InvalidDefinedFieldsException::class);
    }

    protected function validate()
    {
        abc($this->clientInput, 'string');
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
