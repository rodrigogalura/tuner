<?php

namespace Tuner;

use Tuner\Exceptions\TunerException;

/**
 * @internal
 */
class RequestsContainer
{
    private array $requests = [];

    public function bind(string $key, callable $factory): void
    {
        $this->requests[$key] = $factory;
    }

    private function resolve(string $key)
    {
        when_not_set($this->requests[$key], fn () => throw new TunerException("Target request [{$key}] does not exist."));

        $factory = $this->requests[$key];

        return $factory($this);
    }

    public function resolveAndRunCallbackWhenRequested(string $key, callable $callback)
    {
        $request = $this->resolve($key);

        when_not_empty($request(), function () use ($request, $key, $callback): TunerBuilder {
            switch ($key) {
                case Tuner::CONFIG_EXPANSION:
                    return $callback($request(), $request->getExpandableRelations());

                default:
                    return $callback($request());
            }
        });
    }

    public static function create()
    {
        return new static;
    }
}
