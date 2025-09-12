<?php

namespace Tuner\ValueObjects;

class ArrayParser extends Parser
{
    public function sanitize(): self
    {
        return $this->set(array_filter(array_map('trim', $this->value)));
    }

    public function intersectTo(array $to): self
    {
        return $this->set(array_intersect($this->value, $to));
    }

    public function exceptFrom(array $from): self
    {
        return $this->set(array_diff($from, $this->value));
    }

    public function implode($glue = ', '): self
    {
        return $this->set(implode($glue, $this->value));
    }
}
