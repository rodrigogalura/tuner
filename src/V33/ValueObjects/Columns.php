<?php

namespace Tuner\Tuner\V33\ValueObjects;

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
        $this->parser->intersectTo($this->visibleColumns);

        return $this;
    }

    public function except()
    {
        $this->parser->exceptFrom($this->visibleColumns);

        return $this;
    }

    public function implode($glue = ', ')
    {
        $this->parser->implode($glue);

        return $this;
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
