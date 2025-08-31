<?php

namespace Laradigs\Tweaker\V32\ValueObjects;

class ProjectionInput
{
    const INTERSECT_KEY = 'intersect_key';
    const EXCEPT_KEY = 'except_key';

    private array $inputUsed;

    public function __construct(
        protected array $config,
        protected array $input
    )
    {
        $this->inputUsed = array_keys($this->input);
        $this->validate();
    }

    private function validate()
    {
        if ($this->intersectIsUse() && $this->exceptIsUse()) {
            throw new \Exception('Cannot use ' . $this->config[static::INTERSECT_KEY] . ' and ' . $this->config[static::EXCEPT_KEY] . ' at the same time!');
        }
    }

    private function intersectIsUse()
    {
        return in_array($this->config[static::INTERSECT_KEY], $this->inputUsed);
    }

    public function exceptIsUse()
    {
        return in_array($this->config[static::EXCEPT_KEY], $this->inputUsed);
    }

    public function used()
    {
        if ($this->intersectIsUse()) {
            return str(static::INTERSECT_KEY)->chopEnd('_key')->value;
        }

        if ($this->exceptIsUse()) {
            return str(static::EXCEPT_KEY)->chopEnd('_key')->value;
        }

        return null;
    }

    public function getValue()
    {
        return array_shift($this->input);
    }
}
