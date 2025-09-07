<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects;

use Exception;
use LogicException;

use function RGalura\ApiIgniter\any;

class DefinedColumns extends Columns
{
    const ERR_CODE_QUERY_EXCEPTION = 11;

    const ERR_MSG_QUERY_EXCEPTION = 'Defined columns are empty!';

    const ERR_CODE_DCOLS_VCOLS_NO_MATCH = 22;

    const ERR_MSG_DCOLS_VCOLS_NO_MATCH = 'Defined columns are invalid. It must be at least one match in visible columns!';

    public function __construct(array $columns, array $visibleColumns)
    {
        parent::__construct($columns, $visibleColumns);

        throw_if(empty($this->columns), new Exception(static::ERR_MSG_QUERY_EXCEPTION, static::ERR_CODE_QUERY_EXCEPTION));

        $this->validate();
    }

    private function validate()
    {
        throw_unless(any(parent::__invoke(), $this->visibleColumns), new LogicException(static::ERR_MSG_DCOLS_VCOLS_NO_MATCH, static::ERR_CODE_DCOLS_VCOLS_NO_MATCH));
    }
}
