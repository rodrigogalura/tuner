<?php

namespace Laradigs\Tweaker\Projection;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class ProjectionField extends Projection
{
    public function __construct(
        Model $model,
        array $projectableFields,
        array $definedFields,
        private array $clientInput,
    ) {
        parent::__construct($model, $projectableFields, $definedFields);
    }

    protected function validate()
    {
        if (empty($this->clientInput)) {
            throw new Exception(code: Projection::NO_ACTION_WILL_PERFORM_CODE);
        }

        parent::validate();
    }

    public function project()
    {
        try {
            $this->validate();

            return $this->clientInput === ['*']
                ? $this->projectableFields
                : array_values(array_intersect($this->projectableFields, $this->clientInput));

        } catch (Throwable $e) {
            throw_if($e->getCode() !== static::NO_ACTION_WILL_PERFORM_CODE, $e);

            return;
        }
    }
}
