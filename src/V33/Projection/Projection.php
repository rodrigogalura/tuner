<?php

namespace Laradigs\Tweaker\V33\Projection;

abstract class Projection implements Projectable
{
    public function __construct(protected array $from, protected array $to)
    {
        //
        logger()->debug(print_r($from, true));
        logger()->debug(print_r($to, true));
    }

    public static function from(array $from)
    {
        return new static($from, []);
    }

    public function to(array $to)
    {
        $this->to = $to;
        return $this;
    }

    abstract public function project();
}
