<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Model;
use function RGalura\ApiIgniter\filter_explode;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

class Projection
{
    private readonly array $projectedFields;

    private string $clientInputKey = 'fields';
    private string $clientInputNotKey = 'fields!';

    public function __construct(
        private Model $model,
        private array $projectableFields,
        private array $definedFields,
        private array $clientInput,
    )
    {
        //
    }

    public function setClientInputFieldsKey($key)
    {
        $this->clientInputKey = $key;
    }

    public function setClientInputFieldsNotKey($key)
    {
        $this->clientInputNotKey = $key;
    }

    private function convertToValuesIfAsterisk(&$var)
    {
        if ($var === ['*']) {
            $var = $this->visibleFields();
        }
    }

    public function visibleFields()
    {
        return $this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());
    }

    private function throwIfInvalidFields(array $fields)
    {
        if (! empty($diff = array_diff($fields, $this->visibleFields()))) {
            throw new InvalidFieldsException($diff, 1);
        }
    }

    /**
     * Execute the projection logic
     *
     * @throws \RGalura\ApiIgniter\Exceptions\InvalidFieldsException
     * @return void
     */
    public function handle()
    {
        if (empty($this->projectableFields)) {
            return;
        }

        $include = $this->clientInput[$this->clientInputKey] ?? null;
        $exclude = $this->clientInput[$this->clientInputNotKey] ?? null;

        if (isset($include, $exclude)) {
            return;
        }

        $this->convertToValuesIfAsterisk($this->projectableFields);
        $this->throwIfInvalidFields($this->projectableFields);

        $this->convertToValuesIfAsterisk($this->definedFields);
        $this->throwIfInvalidFields($this->definedFields);

        if (empty($this->projectableFields = array_values(array_intersect($this->projectableFields, $this->definedFields)))) {
            return;
        }

        $includeFn = function (array $include) {
            return $include === ['*']
                ? $this->projectableFields
                : array_values(array_intersect($this->projectableFields, $include));
        };

        $excludeFn = function (array $exclude) {
            return $exclude === ['*']
                ? []
                : array_values(array_diff($this->projectableFields, $exclude));
        };

        $this->projectedFields = match (true) {
            isset($include) => $includeFn(filter_explode($include)),
            isset($exclude) => $excludeFn(filter_explode($exclude)),
            default => null
        };

        return $this;
    }

    public function getProjectedFields()
    {
        return $this->projectedFields;
    }
}
