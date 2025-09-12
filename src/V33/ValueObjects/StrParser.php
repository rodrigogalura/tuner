<?php

namespace Tuner\Tuner\V33\ValueObjects;

class StrParser extends Parser
{
    private readonly ArrayParser $arrayParser;

    private function arrayParserCreated()
    {
        return ! is_null($this->arrayParser ?? null);
    }

    public function __call(string $method, array $arguments)
    {
        if ($this->arrayParserCreated()) {
            empty($arguments)
                ? $this->arrayParser->{$method}()
                : $this->arrayParser->{$method}($arguments);

            return $this->arrayParser;
        }
    }

    public function explode($delimiter = ', '): self
    {
        $this->value = explode($delimiter, $this->value);

        return $this;
    }

    public function arrayParser()
    {
        $this->arrayParser = ArrayParser::create($this->value);

        return $this;
    }
}
