<?php

namespace Laradigs\Tweaker\V31\Projection;

use function RGalura\ApiIgniter\filter_explode;

class Intersect extends Projection
{
    public function __construct(
        array $columns,
        mixed $projectableColumns,
        mixed $definedColumns,
        array $clientInput,
    ) {
        parent::__construct($columns, $projectableColumns, $definedColumns, $clientInput);
    }

    protected function validate()
    {
        parent::prerequisites();
        parent::validate();
    }

    public function project()
    {
        $this->validate();

        $inputArr = filter_explode($this->clientInputValue);

        return $inputArr === ['*']
            ? $this->projectableColumns
            : $this->truthTable->intersect($this->projectableColumns, $inputArr);
    }
}
