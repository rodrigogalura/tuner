<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use function RGalura\ApiIgniter\assign_if;
use function RGalura\ApiIgniter\filter_explode;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\ImproperUsedProjectionException;

class Projection
{
    private readonly array $selectFields;

    public function __construct(
        private Model $model,
        private readonly array $projectableFields,
        private readonly array $definedFields,
        private readonly array $clientInput
    )
    {
        //
    }

    private function columnListing()
    {
        return $this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());
    }

    private function allVisibleFields()
    {
        return array_diff(
            $this->columnListing(),
            $this->model->getHidden()
        );
    }

    private function convertToValuesIfAsterisk(&$var)
    {
        if ($var === ['*']) {
            $var = $this->allVisibleFields();
        }
    }

    private function throwIfInvalidFields(array $fields)
    {
        dd($this->columnListing());
        if (! empty($diff = array_diff($fields, $this->columnListing()))) {
            throw new InvalidFieldsException($diff, 1);
        }
    }

    /**
     * @throws \RGalura\ApiIgniter\Exceptions\InvalidFieldsException
     * @return $this|null
     */
    public function setSelectFields($includeFieldsKey = 'fields', $excludeFieldsKey = 'fields!')
    {
        if (empty($this->projectableFields)) {
            return;
        }

        $projectable = $this->projectableFields;
        $definedFields = $this->definedFields;

        // $builder->getQuery()->columns

        $this->convertToValuesIfAsterisk($projectable);
        $this->throwIfInvalidFields($projectable);
        die;

        // if ($definedFields = ($this->model->getQuery()->columns ?? null)) {
            $this->convertToValuesIfAsterisk($definedFields);
            $this->throwIfInvalidFields($definedFields);
        // }

        $projectable = array_intersect($projectable, $definedFields);

        $includeFn = function (array $include) use ($projectable) {
            return match (true) {
                $include === ['*'] => $projectable,
                // ! empty($diff = array_diff($include, $projectable)) => throw new InvalidFieldsException(array_values($diff)),
                default => array_intersect($projectable, $include)
            };
        };

        // $excludeFn = function (array $projectable, array $exclude) {
        //     return match (true) {
        //         $exclude === ['*'] => throw new ExcludeFieldsException($exclude),
        //         ! empty($diff = array_diff($exclude, $projectable)) => throw new InvalidFieldsException(array_values($diff)),
        //         default => array_diff($projectable, $exclude)
        //     };
        // };

        $include = $this->clientInput[$includeFieldsKey] ?? null;
        $exclude = $this->clientInput[$excludeFieldsKey] ?? null;

        $this->selectFields = match (true) {
            isset($include) && isset($exclude) => throw new ImproperUsedProjectionException($includeFieldsKey, $excludeFieldsKey),
            isset($include) => $includeFn(filter_explode($include)),
            // isset($exclude) => $excludeFn($projectable, filter_explode($exclude)),
            default => null
        };

        return $this;
    }

    public function getSelectFields()
    {
        return $this->selectFields;
    }
}
