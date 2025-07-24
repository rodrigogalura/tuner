<?php

namespace Laradigs\Tweaker\Projection;

use function RGalura\ApiIgniter\abc;
use function RGalura\ApiIgniter\filter_explode;

class ExceptProjection extends Projection
{
    public function __construct(
        array $visibleFields,
        array $projectableFields,
        array $definedFields,
        private array $clientInput,
    ) {
        parent::__construct($visibleFields, $projectableFields, $definedFields, $clientInput);
    }

    protected function validate()
    {
        parent::prerequisites();
        parent::validate();

        abc($this->clientInput, 'not_in:*', "The {$this->key} must not use asterisk(*).");
    }

    public function project()
    {
        $this->validate();

        return $this->truthTable->except($this->projectableFields, filter_explode($this->clientInputValue));
    }
}
