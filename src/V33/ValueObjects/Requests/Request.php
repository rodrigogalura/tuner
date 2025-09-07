<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

abstract class Request
{
    public function __construct(
        protected string|array $key,
        protected array $request
    ) {
        if (! is_a($this, RequestInterface::class)) {
            throw new \LogicException('The '.$this::class.' must be implementation of '.RequestInterface::class);
        }

        $this->beforeValidate();

        if ($this->hasRequest()) {
            $this->validate();
        }
    }

    private function hasRequest()
    {
        return count($this->request) > 0;
    }

    protected function beforeValidate()
    {
        //
    }

    public function __invoke(): array
    {
        return $this->request;
    }

    abstract protected function validate();
}
