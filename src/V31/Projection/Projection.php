<?php

namespace Laradigs\Tweaker\V31\Projection;

use Exceptions;
use Laradigs\Tweaker\TruthTable;
use Laradigs\Tweaker\DisabledException;
use function RGalura\ApiIgniter\validate;
use Laradigs\Tweaker\V31\Projection\ProjectionError as E;
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
        protected array $columns,
        protected mixed $projectableColumns,
        protected mixed $definedColumns,
        private array $clientInput,
    ) {
        $this->truthTable = new TruthTable($columns);

        $this->key = key($clientInput);
        $this->clientInputValue = static::$clientInputs[$this->key] = current($clientInput);
    }

    // private function throwIfNotInColumns(array $fields, $exception)
    // {
    //     throw_if($diff = $this->truthTable->diffFromAllItems($fields), new $exception($diff));
    // }

    // private function throwIfSomeNotInVisibleColumns(array $fields, $exception)
    // {
    //     throw_if($diff = $this->truthTable->diffFromAllItems($fields), new $exception($diff));
    // }

    protected function prerequisites()
    {
        # Projectable
        throw_if(empty($this->projectableColumns), E::P_Disabled->exception2());

        $this->truthTable->extractIfAsterisk($this->projectableColumns);
        $diff = $this->truthTable->diffFromAllItems($this->projectableColumns);
        throw_if(!empty($diff), E::P_NotInColumns->exception2(invalidColumns: $diff));

        $this->truthTable->intersectToAllItems($this->projectableColumns);

        # Defined
        throw_if(empty($this->definedColumns), E::Q_LaravelDefaultError->exception2());

        $this->truthTable->extractIfAsterisk($this->definedColumns);
        $diff = $this->truthTable->diffFromAllItems($this->definedColumns);
        throw_if(!empty($diff), E::Q_NotInColumns->exception2(invalidColumns: $diff));

        $this->projectableColumns = $this->truthTable->intersect($this->projectableColumns, $this->definedColumns);
        throw_if(empty($this->projectableColumns), E::Q_NotInProjectable->exception2());
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
