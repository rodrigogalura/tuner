<?php

namespace Laradigs\Tweaker;

use Exception;
use Illuminate\Database\Eloquent\Model;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\NoDefinedFieldException;
use Throwable;

abstract class Projection
{
    private readonly array $projectedFields;

    // private string $clientInputKey = 'fields';

    // private string $clientInputNotKey = 'fields!';

    // private ?string $include;

    // private ?string $exclude;

    const NO_ACTION_WILL_PERFORM_CODE = -1;

    public function __construct(
        private Model $model,
        private array $projectableFields,
        private array $definedFields,
        // private array $clientInput,
    ) {
        // $this->include = $this->clientInput[$this->clientInputKey] ?? null;
        // $this->exclude = $this->clientInput[$this->clientInputNotKey] ?? null;
    }

    // public function setClientInputFieldsKey($key)
    // {
    //     $this->clientInputKey = $key;
    // }

    // public function setClientInputFieldsNotKey($key)
    // {
    //     $this->clientInputNotKey = $key;
    // }

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
        // if (! (isset($this->include) xor isset($this->exclude))) {
        //     throw new Exception(code: static::NO_ACTION_WILL_PERFORM_CODE);
        // }

        // if ($this->include === '') {
        //     throw new Exception(code: static::NO_ACTION_WILL_PERFORM_CODE);
        // }

        // if ($this->exclude === '*') {
        //     throw new Exception(code: static::NO_ACTION_WILL_PERFORM_CODE);
        // }

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

    /**
     * Execute the projection logic
     *
     * @return $this|null
     *
     * @throws \RGalura\ApiIgniter\Exceptions\InvalidFieldsException
     * @throws \RGalura\ApiIgniter\Exceptions\NoDefinedFieldException
     */
    // public function handle()
    // {
    //     try {
    //         $this->validate();
    //     } catch (Throwable $e) {
    //         throw_if($e->getCode() !== static::NO_ACTION_WILL_PERFORM_CODE, $e);

    //         return;
    //     }

    //     $includeFn = function (array $includeArr) {
    //         return $includeArr === ['*']
    //             ? $this->projectableFields
    //             : array_values(array_intersect($this->projectableFields, $includeArr));
    //     };

    //     $excludeFn = function (array $excludeArr) {
    //         return array_values(array_diff($this->projectableFields, $excludeArr));
    //     };

    //     $this->projectedFields = match (true) {
    //         isset($this->include) => $includeFn(filter_explode($this->include)),
    //         isset($this->exclude) => $excludeFn(filter_explode($this->exclude)),
    //     };

    //     return $this;
    // }

    public function getProjectedFields()
    {
        return $this->projectedFields;
    }
}
