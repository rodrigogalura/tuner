<?php

namespace Laradigs\Tweaker\V33\ValueObjects;

use Exception;
use function RGalura\ApiIgniter\any;
use Laradigs\Tweaker\V33\ValueObjects\ArrayParser;

class Columns
{
    const ALL_VISIBLE_COLUMNS_ALIAS = ['*'];

    protected readonly array $parsedColumns;

    public function __construct(
        protected array $columns,
        protected array $visibleColumns
    )
    {
        //
    }

    public function getParsedColumns()
    {
        $this->parsedColumns ??=
            (new ArrayParser($this->columns))
            ->assignIfEqTo(static::ALL_VISIBLE_COLUMNS_ALIAS, $this->visibleColumns)
            ->sanitize()
            ->get();

        return $this->parsedColumns;
    }
}
