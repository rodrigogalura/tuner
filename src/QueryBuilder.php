<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RGalura\ApiIgniter\Exceptions\ImproperUsedProjectionException;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

use function RGalura\ApiIgniter\filter_explode;

class QueryBuilder
{
    private readonly array $selectFields;

    public function __construct(
        // private Builder $builder,
        private Model $model,
        private array $clientInput
    ) {
        //
    }

    public function getSelectFields()
    {
        return $this->selectFields;
    }

    // public function getBuilder()
    // {
    //     return $this->builder;
    // }

    private function throwIfInvalidFields(array $fields)
    {
        if (! empty($diff = array_diff($fields, $this->model->columnListing()))) {
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
            $this->getHidden()
        );
    }

    /**
     * @return $this
     *
     * @throws \RGalura\ApiIgniter\Exceptions\InvalidFieldsException
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
            $this->throwIfInvalidFields($definedFields);
        }

        $projectableFields = array_intersect($projectableFields, $definedFields);

        $includeFn = function (array $projectableFields, array $include) {
            return match (true) {
                $include === ['*'] => $projectableFields,
                ! empty($diff = array_diff($include, $projectableFields)) => throw new InvalidFieldsException(array_values($diff)),
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

        // if (! is_null($this->selectFields)) {
        //     // dd($this->builder);

        //     // $combinedFieldsResult = array_diff($this->givenFields, $projectableFields) + $this->selectFields;

        //     // ksort($combinedFieldsResult, SORT_NUMERIC);

        //     // $this->builder->select($this->selectFields);
        //     // die;

        // }

        return $this;
    }

    public function execute()
    {
        // if ($debuggable && ($_GET['debug'] ?? false)) {
        //     return
        //         print_r(['with' => self::$expand], true).PHP_EOL.
        //         $builder->toSql();
        // }

        // if ($paginatable && ($perPage = $_GET['per-page'] ?? false)) {
        //     return $builder->paginate($perPage);
        // }

        // $this->builder->get();
    }
}
