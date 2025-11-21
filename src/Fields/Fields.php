<?php

namespace Tuner\Fields;

use Tuner\Parsers\ArrayParser;

/**
 * @internal
 */
class Fields
{
    const ALL_VISIBLE_COLUMNS_ALIAS = ['*'];

    private ArrayParser $parser;

    public function __construct(
        protected array $fields,
        protected array $visibleFields
    ) {
        $this->parser = ArrayParser::create($this->fields)
            ->assignIfEqTo(static::ALL_VISIBLE_COLUMNS_ALIAS, $this->visibleFields)
            ->sanitize();
    }

    public function intersect()
    {
        return $this->parser->intersectTo($this->visibleFields);
    }

    public function except()
    {
        return $this->parser->exceptFrom($this->visibleFields);
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
