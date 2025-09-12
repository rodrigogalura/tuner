<?php

namespace Tuner\Columns;

use Exception;

use function Tuner\any;

/**
 * @internal
 */
class SearchableColumns extends Columns
{
    const ERR_CODE_DISABLED = 5;

    const ERR_MSG_DISABLED = 'SearchableColumns columns are empty!';

    const ERR_CODE_PCOLS_VCOLS_NO_MATCH = 6;

    const ERR_MSG_PCOLS_VCOLS_NO_MATCH = 'SearchableColumns columns are invalid. It must be at least one match in visible columns!';

    public function __construct(array $columns, array $visibleColumns)
    {
        parent::__construct($columns, $visibleColumns);

        $this->validate();
    }

    private function validate()
    {
        throw_if(empty($this->columns), new Exception(static::ERR_MSG_DISABLED, static::ERR_CODE_DISABLED));

        throw_unless(any(parent::__invoke(), $this->visibleColumns), new Exception(static::ERR_MSG_PCOLS_VCOLS_NO_MATCH, static::ERR_CODE_PCOLS_VCOLS_NO_MATCH));
    }
}
