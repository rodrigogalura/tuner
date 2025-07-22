<?php

namespace Laradigs\Tweaker\Projection;

use Laradigs\Tweaker\TruthTable;
use Illuminate\Database\Eloquent\Model;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\NoDefinedFieldException;

// abstract class Projection extends TruthTable
abstract class Projection
{
    protected TruthTable $truthTable;

    public function __construct(
        private Model $model,
        protected array $projectableFields,
        protected array $definedFields,
        protected mixed $clientInput,
    ) {
        $this->truthTable = new TruthTable(
            $model->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($model->getTable())
        );
    }

    private function throwIfNotInVisibleFields(array $fields)
    {
        throw_if($diff = $this->truthTable->diffFromAllItems($fields), InvalidFieldsException::class, $diff);
    }

    protected function prerequisites()
    {
        // Make sure client input type is string
        throw_if(!is_string($this->clientInput), NoActionWillPerformException::class);
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

    abstract public function project();
}
