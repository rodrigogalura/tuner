<?php

namespace Laradigs\Tweaker\Projection;

use Illuminate\Validation\Rule;
use function RGalura\ApiIgniter\validate;
use function RGalura\ApiIgniter\filter_explode;

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

        // validate($this->clientInput, 'not_in:*', "The {$this->key} must not use asterisk(*).");

        // dump($this->clientInput, $this->projectableFields, [$this->key => filter_explode($this->clientInputValue)]);

        // validate([$this->key => filter_explode($this->clientInputValue)], Rule::notIn($this->projectableFields), "The {$this->key} must not use asterisk(*).");

        // dd($this->clientInput);

        // dd([$this->key => filter_explode($this->clientInputValue)]);

        validate(
            [$this->key => filter_explode($this->clientInputValue)],
            Rule::notIn($this->projectableFields)
        );
    }

    public function project()
    {
        $this->validate();

        return $this->truthTable->except($this->projectableFields, filter_explode($this->clientInputValue));
    }
}
