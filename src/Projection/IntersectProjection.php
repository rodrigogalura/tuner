<?php

namespace Laradigs\Tweaker\Projection;

use function RGalura\ApiIgniter\filter_explode;

class IntersectProjection extends Projection
{
    public function __construct(
        array $visibleFields,
        array $projectableFields,
        array $definedFields,
        array $clientInput,
    ) {
        parent::__construct($visibleFields, $projectableFields, $definedFields, $clientInput);
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
