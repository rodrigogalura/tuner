<?php

namespace Laradigs\Tweaker\Projection;

use Illuminate\Database\Eloquent\Model;

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
            throw new NoActionWillPerformException;
        }

        parent::validate();
    }

    public function project()
    {
        $this->validate();

        return $this->clientInput === ['*']
            ? $this->projectableFields
            : $this->truthTable->intersect($this->projectableFields, $this->clientInput);
    }
}
