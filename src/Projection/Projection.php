<?php

namespace Laradigs\Tweaker\Projection;

use Exception;
use Illuminate\Database\Eloquent\Model;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\NoDefinedFieldException;

abstract class Projection
{
    const NO_ACTION_WILL_PERFORM_CODE = -1;
    const EMPTY_VALUE = [];

    public function __construct(
        private Model $model,
        protected array $projectableFields,
        protected array $definedFields,
    ) {
        //
    }

    private function extractIfAsterisk(&$var)
    {
        if ($var === ['*']) {
            $var = $this->visibleFields();
        }
    }

    private function throwIfNotInVisibleFields(array $fields)
    {
        if (! empty($diff = array_diff($fields, $this->visibleFields()))) {
            throw new InvalidFieldsException($diff, 1);
        }
    }

    protected function validate()
    {
        if (empty($this->projectableFields)) {
            throw new Exception(code: static::NO_ACTION_WILL_PERFORM_CODE);
        }

        $this->extractIfAsterisk($this->projectableFields);
        $this->throwIfNotInVisibleFields($this->projectableFields);

        if (empty($this->definedFields)) {
            throw new NoDefinedFieldException;
        }

        $this->extractIfAsterisk($this->definedFields);
        $this->throwIfNotInVisibleFields($this->definedFields);

        if (empty($this->projectableFields = array_values(array_intersect($this->projectableFields, $this->definedFields)))) {
            throw new Exception(code: static::NO_ACTION_WILL_PERFORM_CODE);
        }
    }

    public function visibleFields()
    {
        return $this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());
    }

    abstract public function project();
}
