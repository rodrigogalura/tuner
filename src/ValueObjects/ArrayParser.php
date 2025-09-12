<?php

namespace Tuner\ValueObjects;

class ArrayParser extends Parser
{
    public function sanitize(): self
    {
        $this->set(array_filter(array_map('trim', $this->value)));

        return $this;
    }

    // public function intersectTo(array $to): self
    // {
    //     $this->value = array_intersect($this->value, $to);

    //     return $this;
    // }

    // public function exceptFrom(array $from): self
    // {
    //     $this->value = array_diff($from, $this->value);

    //     return $this;
    // }

    // public function implode($glue = ', '): self
    // {
    //     $this->value = implode($glue, $this->value);

    //     return $this;
    // }
}
