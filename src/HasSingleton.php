<?php

namespace Tuner;

use Tuner\Exceptions\TunerException;

/**
 * @internal
 */
trait HasSingleton
{
    const ERR_CODE_DISABLED = 9;

    const ERR_MSG_DISABLED = 'Cannot create multiple Tuner Builder.';

    private static array $instances = [];

    private static function addInstance($instance)
    {
        logger()->debug(empty(static::$instances));

        throw_unless(empty(static::$instances), new TunerException(static::ERR_MSG_DISABLED, static::ERR_CODE_DISABLED));
        array_push(static::$instances, $instance);
    }

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    private function __construct() {}

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone() {}

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }
}
