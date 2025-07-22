<?php

namespace Laradigs\Tweaker\Projection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

use function RGalura\ApiIgniter\filter_explode;

class ExceptProjection extends Projection
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

        throw_if($this->clientInputValue === '*', NoActionWillPerformException::class);

        parent::validate();
    }

    public function project()
    {
        $this->validate();

        return $this->truthTable->except($this->projectableFields, filter_explode($this->clientInputValue));
    }
}
