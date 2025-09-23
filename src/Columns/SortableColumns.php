<?php

namespace Tuner\Columns;

use Tuner\Exceptions\TunerException;

use function Tuner\any;

/**
 * @internal
 */
class SortableColumns extends Columns
{
    const ERR_CODE_DISABLED = 5;

    const ERR_MSG_DISABLED = 'Sortable columns are empty!';

    const ERR_CODE_PCOLS_VCOLS_NO_MATCH = 6;

    const ERR_MSG_PCOLS_VCOLS_NO_MATCH = 'Sortable columns are invalid. It must be at least one match in visible columns!';

    public function __construct(array $columns, array $visibleColumns)
    {
        parent::__construct($columns, $visibleColumns);

        $this->validate();
    }

    private function validate()
    {
        throw_if(empty($this->columns), new TunerException(static::ERR_MSG_DISABLED, static::ERR_CODE_DISABLED));

        throw_unless(any(parent::__invoke(), $this->visibleColumns), new TunerException(static::ERR_MSG_PCOLS_VCOLS_NO_MATCH, static::ERR_CODE_PCOLS_VCOLS_NO_MATCH));
    }
}
