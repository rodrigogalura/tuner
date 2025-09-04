<?php

namespace Laradigs\Tweaker\V33\ValueObjects;

use Exception;
use LogicException;
use function RGalura\ApiIgniter\any;

class ProjectableColumns extends Columns
{
    const ERR_CODE_DISABLED = 1;

    const ERR_MSG_DISABLED = 'Projectable columns are empty!';

    const ERR_CODE_PCOLS_VCOLS_NO_MATCH = 2;

    const ERR_MSG_PCOLS_VCOLS_NO_MATCH = 'Projectable columns are invalid. It must be at least one match in visible columns!';

    public function __construct(array $columns, array $visibleColumns)
    {
        parent::__construct($columns, $visibleColumns);

        throw_if(empty($this->columns), new Exception(static::ERR_MSG_DISABLED, static::ERR_CODE_DISABLED));

        $this->throwUnlessHasAnyValidColumns();
    }

    private function throwUnlessHasAnyValidColumns()
    {
        throw_unless(any($this->getParsedColumns(), $this->visibleColumns), new LogicException(static::ERR_MSG_PCOLS_VCOLS_NO_MATCH, static::ERR_CODE_PCOLS_VCOLS_NO_MATCH));
    }
}
