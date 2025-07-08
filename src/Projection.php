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
        private array $clientInput
    )
    {
        //
    }

    private function throwIfInvalidFields(array $fields)
    {
        if (! empty($diff = array_diff($fields, $this->allFields()))) {
            throw new InvalidFieldsException($diff, 1);
        }
    }

    private function allFields()
    {
        return $this->model->columnListing();
    }

    private function allVisibleFields()
    {
        return array_diff(
            $this->allFields(),
            $this->model->getHidden()
        );
    }

    /**
     * @param  $projectableFields
     * @throws \RGalura\ApiIgniter\Exceptions\InvalidFieldsException
     * @return $this|null
     */
    public function setSelectFields(?array $projectableFields, $includeFieldsKey = 'fields', $excludeFieldsKey = 'fields!')
    {
        if (empty($projectableFields)) {
            return;
        }

        if ($projectableFields === ['*']) {
            $projectableFields = $this->allVisibleFields();
        }

        $this->throwIfInvalidFields($projectableFields);

        if ($definedFields = ($this->model->getQuery()->columns ?? null)) {
            if ($definedFields === ['*']) {
                $definedFields = $this->allVisibleFields();
            }

            $this->throwIfInvalidFields($definedFields);
        }

        $projectableFields = array_intersect($projectableFields, $definedFields);

        $includeFn = function (array $projectableFields, array $include) {
            return match (true) {
                $include === ['*'] => $projectableFields,
                // ! empty($diff = array_diff($include, $projectableFields)) => throw new InvalidFieldsException(array_values($diff)),
                default => array_intersect($projectableFields, $include)
            };
        };

        // $excludeFn = function (array $projectableFields, array $exclude) {
        //     return match (true) {
        //         $exclude === ['*'] => throw new ExcludeFieldsException($exclude),
        //         ! empty($diff = array_diff($exclude, $projectableFields)) => throw new InvalidFieldsException(array_values($diff)),
        //         default => array_diff($projectableFields, $exclude)
        //     };
        // };

        $include = $this->clientInput[$includeFieldsKey] ?? null;
        $exclude = $this->clientInput[$excludeFieldsKey] ?? null;

        $this->selectFields = match (true) {
            isset($include) && isset($exclude) => throw new ImproperUsedProjectionException($includeFieldsKey, $excludeFieldsKey),
            isset($include) => $includeFn($projectableFields, filter_explode($include)),
            // isset($exclude) => $excludeFn($projectableFields, filter_explode($exclude)),
            default => null
        };

        return $this;
    }

    public function getSelectFields()
    {
        return $this->selectFields;
    }
}
