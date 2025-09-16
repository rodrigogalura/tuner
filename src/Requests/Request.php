<?php

namespace Tuner\Requests;

use Tuner\Exceptions\TunerException;

/**
 * @internal
 */
abstract class Request
{
    public function __construct(
        protected string|array $key,
        protected array $request
    ) {
        if (! is_a($this, RequestInterface::class)) {
            throw new TunerException('The ['.$this::class.'] must be implementation of ['.RequestInterface::class.']');
        }

        $this->filterRequest();

        if ($this->shouldValidate() && $this->hasRequest()) {
            // logger()->info('Request from ['.class_basename($this::class).']');
            $this->validate();
        }
    }

    private function hasRequest()
    {
        return count($this->request) > 0;
    }

    private function filterRequest()
    {
        $conditionFn = match (gettype($this->key)) {
            'string' => fn ($paramKey): bool => $paramKey === $this->key,
            'array' => fn ($paramKey): bool => in_array($paramKey, $this->key),
        };

        $this->request = array_filter($this->request, fn ($paramKey): bool => $conditionFn($paramKey), ARRAY_FILTER_USE_KEY);
    }

    protected function shouldValidate()
    {
        return true;
    }

    public function __invoke(): array
    {
        return $this->request;
    }

    abstract protected function validate();
}
