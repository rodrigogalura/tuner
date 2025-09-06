<?php

namespace Laradigs\Tweaker\V33\ValueObjects;

class Columns
{
    const ALL_VISIBLE_COLUMNS_ALIAS = ['*'];

    protected readonly array $parsedColumns;

    public function __construct(
        protected array $columns,
        protected array $visibleColumns
    ) {
        //
    }

    public function __invoke()
    {
        $this->parsedColumns ??=
            (new ArrayParser($this->columns))
                ->assignIfEqTo(static::ALL_VISIBLE_COLUMNS_ALIAS, $this->visibleColumns)
                ->sanitize()
                ->intersectTo($this->visibleColumns)
                ->get();

        return $this->parsedColumns;
    }
}
