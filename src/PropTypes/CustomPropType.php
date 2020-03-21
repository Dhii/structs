<?php

namespace Dhii\Structs\PropTypes;

use Dhii\Structs\PropType;
use TypeError;

/**
 * A generic implementation of a prop type that allows you to dynamically create new prop types.
 *
 * @since [*next-version*]
 */
class CustomPropType implements PropType
{
    /**
     * @since [*next-version*]
     *
     * @var string
     */
    protected $name;

    /**
     * @since [*next-version*]
     *
     * @var callable
     */
    protected $castFn;

    /**
     * @since [*next-version*]
     *
     * @var mixed
     */
    protected $default;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string   $name    The name of the type.
     * @param callable $castFn  A function that accepts a value as argument and should return the casted value. If the
     *                          argument value is not of an acceptable type, a {@link TypeError} should be thrown.
     * @param mixed    $default The default value, to use when a property of this type is uninitialized.
     */
    public function __construct(string $name, callable $castFn, $default = null)
    {
        $this->name = $name;
        $this->castFn = $castFn;
        $this->default = $default;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function cast($value)
    {
        return ($this->castFn)($value);
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function isValid($value) : bool
    {
        try {
            $this->cast($value);

            return true;
        } catch (TypeError $error) {
            return false;
        }
    }
}
