<?php

namespace Laradigs\Tweaker\Projection;

use Laradigs\Tweaker\TruthTable;
use Illuminate\Database\Eloquent\Model;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\NoDefinedFieldException;

abstract class Projection extends TruthTable
{
    public function __construct(
        private Model $model,
        protected array $projectableFields,
        protected array $definedFields,
    ) {
        parent::__construct(
            $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable())
        );
    }

    private function throwIfNotInVisibleFields(array $fields)
    {
        if (! empty($diff = $this->diffFromAllItems($fields))) {
            throw new InvalidFieldsException($diff, 1);
        }
    }

    protected function validate()
    {
        if (empty($this->projectableFields)) {
            throw new NoActionWillPerformException;
        }

        $this->extractIfAsterisk($this->projectableFields);
        $this->throwIfNotInVisibleFields($this->projectableFields);

        if (empty($this->definedFields)) {
            throw new NoDefinedFieldException;
        }

        $this->extractIfAsterisk($this->definedFields);
        $this->throwIfNotInVisibleFields($this->definedFields);

        if (empty($this->projectableFields = $this->intersect($this->projectableFields, $this->definedFields))) {
            throw new NoActionWillPerformException;
        }
    }

    abstract public function project();
}
