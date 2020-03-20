<?php

namespace Dhii\Structs\PropTypes;

use Dhii\Structs\PropType;
use Dhii\Structs\Ty;

/**
 * Boolean struct property type.
 *
 * @since [*next-version*]
 */
class BoolPropType implements PropType
{
    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getName() : string
    {
        return 'bool';
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getDefault()
    {
        return false;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function isValid($value) : bool
    {
        return is_scalar($value);
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function cast($value)
    {
        if ($this->isValid($value)) {
            return (bool) $value;
        }

        throw Ty::createTypeError($this, $value);
    }
}
