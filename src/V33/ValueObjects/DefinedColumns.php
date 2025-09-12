<?php

namespace Tuner\V33\ValueObjects;

use Exception;

use function Tuner\V33\any;

class DefinedColumns extends Columns
{
    const ERR_CODE_QUERY_EXCEPTION = 11;

    const ERR_MSG_QUERY_EXCEPTION = 'Defined columns are empty!';

    const ERR_CODE_DCOLS_VCOLS_NO_MATCH = 22;

    const ERR_MSG_DCOLS_VCOLS_NO_MATCH = 'Defined columns are invalid. It must be at least one match in visible columns!';

    public function __construct(array $columns, array $visibleColumns)
    {
        parent::__construct($columns, $visibleColumns);

        $this->validate();
    }

    private function validate()
    {
        throw_if(empty($this->columns), new Exception(static::ERR_MSG_QUERY_EXCEPTION, static::ERR_CODE_QUERY_EXCEPTION));

        throw_unless(any(parent::__invoke(), $this->visibleColumns), new Exception(static::ERR_MSG_DCOLS_VCOLS_NO_MATCH, static::ERR_CODE_DCOLS_VCOLS_NO_MATCH));
    }
}
