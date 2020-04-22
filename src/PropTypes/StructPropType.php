<?php

namespace Dhii\Structs\PropTypes;

use Dhii\Structs\PropType;
use Dhii\Structs\Struct;
use Dhii\Structs\Ty;
use LogicException;

/**
 * A child-struct prop type.
 *
 * This type matches struct instances as well as array values from which a struct instance can be created. It is
 * similar to a union of an {@link ArrayPropType} and an {@link ObjectPropType} (that is restricted to a struct class),
 * with the added capability of automatically casting arrays into struct instances.
 *
 * @since [*next-version*]
 */
class StructPropType implements PropType
{
    /**
     * @since [*next-version*]
     *
     * @var string
     */
    protected $structClass;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $structClass The name of the struct class, which must extend {@link Struct}.
     */
    public function __construct(string $structClass)
    {
        if (!class_exists($structClass, true) || !is_subclass_of($structClass, Struct::class)) {
            throw new LogicException("Class {$structClass} does not extend " . Struct::class);
        }

        $this->structClass = $structClass;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getName() : string
    {
        return $this->structClass;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getDefault()
    {
        return null;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function cast($value)
    {
        if ($value instanceof $this->structClass) {
            return $value;
        }

        if (is_array($value)) {
            return call_user_func([$this->structClass, 'fromArray'], $value);
        }

        throw Ty::createTypeError($this, $value);
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function isValid($value) : bool
    {
        if ($value instanceof $this->structClass) {
            return true;
        }

        if (!is_array($value)) {
            return false;
        }

        /* @var $props PropType[] */
        $props = call_user_func([$this->structClass, 'getPropTypes']);

        foreach ($props as $prop => $type) {
            // If prop is missing from arg value, ignore
            if (!array_key_exists($prop, $value)) {
                continue;
            }

            if (!$type->isValid($value[$prop])) {
                return false;
            }

            unset($value[$prop]);
        }

        // If any keys remain in the value, the value has keys that do not correspond to struct props
        // Therefore, the value is only valid if it's empty here
        return empty($value);
    }
}
