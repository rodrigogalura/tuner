<?php

namespace Laradigs\Tweaker\Projection;

use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\TruthTable;
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
    ) {
        $this->truthTable = new TruthTable(
            $model->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($model->getTable())
        );
        // parent::__construct(
        //     $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable())
        // );
    }

    private function throwIfNotInVisibleFields(array $fields)
    {
        if (! empty($diff = $this->truthTable->diffFromAllItems($fields))) {
            throw new InvalidFieldsException($diff, 1);
        }
    }

    protected function validate()
    {
        if (empty($this->projectableFields)) {
            throw new NoActionWillPerformException;
        }

        $this->truthTable->extractIfAsterisk($this->projectableFields);
        $this->throwIfNotInVisibleFields($this->projectableFields);

        if (empty($this->definedFields)) {
            throw new NoDefinedFieldException;
        }

        $this->truthTable->extractIfAsterisk($this->definedFields);
        $this->throwIfNotInVisibleFields($this->definedFields);

        if (empty($this->projectableFields = $this->truthTable->intersect($this->projectableFields, $this->definedFields))) {
            throw new NoActionWillPerformException;
        }
    }

    abstract public function project();
}
