<?php

namespace Dhii\Structs\Tests\Stubs;

use Dhii\Structs\PropType;
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
    protected static $__propTypes;

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public static function propTypes() : array
    {
        return static::$__propTypes;
    }

    /**
     * Sets the stub's prop types.
     *
     * @since [*next-version*]
     *
     * @param PropType[] $propTypes A mapping of property names to their corresponding types.
     */
    public static function setPropTypes(array $propTypes)
    {
        static::$__propTypes = $propTypes;
    }
}
