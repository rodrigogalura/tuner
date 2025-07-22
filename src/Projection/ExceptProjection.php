<?php

namespace Laradigs\Tweaker\Projection;

use function RGalura\ApiIgniter\filter_explode;

class ExceptProjection extends Projection
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

        throw_if($this->clientInputValue === '*', NoActionWillPerformException::class);

        parent::validate();
    }

    public function project()
    {
        $this->validate();

        return $this->truthTable->except($this->projectableFields, filter_explode($this->clientInputValue));
    }
}
