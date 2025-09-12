<?php

namespace Tuner\Columns;

use Tuner\Parsers\ArrayParser;

/**
 * @internal
 */
class Columns
{
    const ALL_VISIBLE_COLUMNS_ALIAS = ['*'];

    private ArrayParser $parser;

    public function __construct(
        protected array $columns,
        protected array $visibleColumns
    ) {
        $this->parser = ArrayParser::create($this->columns)
            ->assignIfEqTo(static::ALL_VISIBLE_COLUMNS_ALIAS, $this->visibleColumns)
            ->sanitize();
    }

    public function intersect()
    {
        return $this->parser->intersectTo($this->visibleColumns);
    }

    public function except()
    {
        return $this->parser->exceptFrom($this->visibleColumns);
    }

    public function implode($glue = ', ')
    {
        return $this->parser->implode($glue);
    }

    public function get()
    {
        return $this->parser->get();
    }

    public function __invoke()
    {
        return $this->get();
    }
}
