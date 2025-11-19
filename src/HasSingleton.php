<?php

namespace Tuner;

use Tuner\Exceptions\TunerException;

/**
 * @internal
 */
trait HasSingleton
{
    const ERR_CODE_MULTIPLE_BUILDER = 9;

    const ERR_MSG_MULTIPLE_BUILDER = 'Cannot create multiple Tuner Builder.';

    private static $instance;

    private static function saveInstance($instance)
    {
        throw_unless(! is_null(static::$instance), new TunerException(static::ERR_MSG_MULTIPLE_BUILDER, static::ERR_CODE_MULTIPLE_BUILDER));
        static::$instance = $instance;
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

    public function deleteInstance()
    {
        static::$instance = null;
    }
}
