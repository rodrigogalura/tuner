<?php

namespace Laradigs\Tweaker\V31\Projection;

use Laradigs\Tweaker\DisabledException;
use Laradigs\Tweaker\Projection\Exceptions\CannotUseMultipleProjectionException;
use Laradigs\Tweaker\Projection\Exceptions\DefinedFieldsAreEmptyException;
use Laradigs\Tweaker\Projection\Exceptions\InvalidDefinedFieldsException;
use Laradigs\Tweaker\Projection\Exceptions\InvalidProjectableException;
use Laradigs\Tweaker\TruthTable;

use function RGalura\ApiIgniter\validate;

abstract class Projection
{
    use Exportable;

    protected TruthTable $truthTable;

    protected readonly string $key;

    protected readonly mixed $clientInputValue;

    public static $clientInputs = [];

    public function __construct(
        protected array $columns,
        protected mixed $projectableColumns,
        protected mixed $definedColumns,
        private array $clientInput,
    ) {
        $this->truthTable = new TruthTable($columns);

        $this->key = key($clientInput);
        $this->clientInputValue = static::$clientInputs[$this->key] = current($clientInput);
    }

    private function throwIfNotInColumns(array $fields, $exception)
    {
        throw_if($diff = $this->truthTable->diffFromAllItems($fields), new $exception($diff));
    }

    // private function throwIfSomeNotInVisibleColumns(array $fields, $exception)
    // {
    //     throw_if($diff = $this->truthTable->diffFromAllItems($fields), new $exception($diff));
    // }

    protected function prerequisites()
    {
        // Projectable
        throw_if(empty($this->projectableColumns), DisabledException::class);

        $this->truthTable->extractIfAsterisk($this->projectableColumns);
        $this->throwIfNotInColumns($this->projectableColumns, InvalidProjectableException::class);
        $this->truthTable->intersectToAllItems($this->projectableColumns);

        // Defined
        throw_if(empty($this->definedColumns), DefinedFieldsAreEmptyException::class);

        $this->truthTable->extractIfAsterisk($this->definedColumns);
        $this->throwIfNotInColumns($this->definedColumns, InvalidDefinedFieldsException::class);

        $this->projectableColumns = $this->truthTable->intersect($this->projectableColumns, $this->definedColumns);
        throw_if(empty($this->projectableColumns), InvalidDefinedFieldsException::class, $this->definedColumns);
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
