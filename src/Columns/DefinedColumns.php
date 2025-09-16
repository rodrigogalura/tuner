<?php

namespace Tuner\Columns;

use Tuner\Exceptions\TunerException;

use function Tuner\any;

/**
 * @internal
 */
class DefinedColumns extends Columns
{
    const ERR_CODE_QUERY_EXCEPTION = 7;

    const ERR_MSG_QUERY_EXCEPTION = 'Defined columns are empty!';

    const ERR_CODE_DCOLS_VCOLS_NO_MATCH = 8;

    const ERR_MSG_DCOLS_VCOLS_NO_MATCH = 'Defined columns are invalid. It must be at least one match in visible columns!';

    public function __construct(array $columns, array $visibleColumns)
    {
        parent::__construct($columns, $visibleColumns);

        $this->validate();
    }

    private function validate()
    {
        throw_if(empty($this->columns), new TunerException(static::ERR_MSG_QUERY_EXCEPTION, static::ERR_CODE_QUERY_EXCEPTION));

        throw_unless(any(parent::__invoke(), $this->visibleColumns), new TunerException(static::ERR_MSG_DCOLS_VCOLS_NO_MATCH, static::ERR_CODE_DCOLS_VCOLS_NO_MATCH));
    }
}
