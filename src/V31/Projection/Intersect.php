<?php

namespace Laradigs\Tweaker\V31\Projection;

use function RGalura\ApiIgniter\filter_explode;

class Intersect extends Projection
{
    public function __construct(
        array $columns,
        array $projectableColumns,
        array $definedColumns,
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
            : ($this->intersect)($this->projectableColumns, $inputArr);
    }
}
