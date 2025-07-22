<?php

namespace Laradigs\Tweaker\Projection;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use function RGalura\ApiIgniter\filter_explode;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;

class ProjectionField extends Projection
{
    public function __construct(
        Model $model,
        array $projectableFields,
        array $definedFields,
        array $clientInput,
    ) {
        parent::__construct($model, $projectableFields, $definedFields, $clientInput);
    }

    protected function validate()
    {
        parent::prerequisites();

        throw_if(empty($this->clientInputValue), NoActionWillPerformException::class);

        parent::validate();
    }

    public function project()
    {
        $this->validate();

        $inputArr = filter_explode($this->clientInputValue);

        return $inputArr === ['*']
            ? $this->projectableFields
            : $this->truthTable->intersect($this->projectableFields, $inputArr);
    }
}
