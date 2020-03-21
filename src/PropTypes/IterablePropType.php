<?php

namespace Dhii\Structs\PropTypes;

use Dhii\Structs\PropType;
use Dhii\Structs\Ty;

/**
 * Iterable struct property type.
 *
 * @since [*next-version*]
 */
class IterablePropType implements PropType
{
    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getName() : string
    {
        return 'iterable';
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getDefault()
    {
        return [];
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function isValid($value) : bool
    {
        return is_iterable($value);
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function cast($value)
    {
        if ($this->isValid($value)) {
            return $value;
        }

        throw Ty::createTypeError($this, $value);
    }
}
