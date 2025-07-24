<?php

namespace Laradigs\Tweaker\Projection;

use function RGalura\ApiIgniter\filter_explode;
use function RGalura\ApiIgniter\validate;

class ExceptProjection extends Projection
{
    public function __construct(
        array $visibleFields,
        mixed $projectableFields,
        mixed $definedFields,
        private array $clientInput,
    ) {
        parent::__construct($visibleFields, $projectableFields, $definedFields, $clientInput);
    }

    protected function validate()
    {
        parent::prerequisites();
        parent::validate();

        validate($this->clientInput, 'not_in:*', "The {$this->key} must not use asterisk(*).");
    }

    public function project()
    {
        $this->validate();

        return $this->truthTable->except($this->projectableFields, filter_explode($this->clientInputValue));
    }
}
