<?php

namespace Laradigs\Tweaker\Projection;

use Exception;
use Throwable;
use Laradigs\Tweaker\Projection;
use Illuminate\Database\Eloquent\Model;

class ProjectionFieldNot extends Projection {
    public function __construct(
        Model $model,
        private array $projectableFields,
        array $definedFields,
        private array $clientInput,
    )
    {
        parent::__construct($model, $projectableFields, $definedFields);
    }

    protected function validate()
    {
        if ($this->clientInput === ['*']) {
            throw new Exception(code: Projection::NO_ACTION_WILL_PERFORM_CODE);
        }

        parent::validate();
    }

    public function project()
    {
        try {
            $this->validate();

            return array_values(array_diff($this->projectableFields, $this->clientInput));
        } catch (Throwable $e) {
            throw_if($e->getCode() !== static::NO_ACTION_WILL_PERFORM_CODE, $e);

            return;
        }
    }
}
