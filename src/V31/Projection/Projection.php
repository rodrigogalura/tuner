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

    private function throwIfNotInColumns(array $fields, E $e)
    {
        throw_if($diff = $this->truthTable->diffFromAllItems($fields), $e->exception(invalidColumns: $diff));
    }

    private function projectablePrerequisites()
    {
        throw_if(empty($this->projectableColumns), E::P_Disabled->exception());

        $this->truthTable->extractIfAsterisk($this->projectableColumns);
        $this->throwIfNotInColumns($this->projectableColumns, E::P_NotInColumns);

        $this->truthTable->intersectToAllItems($this->projectableColumns);
    }

    private function definedPrerequisites()
    {
        # Defined
        throw_if(empty($this->definedColumns), E::Q_LaravelDefaultError->exception());

        $this->truthTable->extractIfAsterisk($this->definedColumns);
        $this->throwIfNotInColumns($this->definedColumns, E::Q_NotInColumns);

        $this->projectableColumns = $this->truthTable->intersect($this->projectableColumns, $this->definedColumns);
        throw_if(empty($this->projectableColumns), E::Q_NotInProjectable->exception());
    }

    protected function prerequisites()
    {
        $this->projectablePrerequisites();
        $this->definedPrerequisites();
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
