<?php

namespace Dhii\Structs\Tests\Stubs;

use Dhii\Structs\Struct;

/**
 * A generic struct stub implementation.
 *
 * @since [*next-version*]
 */
class StructStub extends Struct
{
    /**
     * @since [*next-version*]
     *
     * @var array
     */
    public static $propTypes;

    /**
     * @since [*next-version*]
     *
     * @var int
     */
    public static $numPropTypesCalled = 0;

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public static function propTypes() : array
    {
        static::$numPropTypesCalled++;

        return static::$propTypes;
    }
}
