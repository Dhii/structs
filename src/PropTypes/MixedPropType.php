<?php

namespace Dhii\Structs\PropTypes;

use Dhii\Structs\PropType;

/**
 * Mixed struct property type.
 *
 * This is effectively equivalent to a property without any type restrictions.
 *
 * @since [*next-version*]
 */
class MixedPropType implements PropType
{
    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getName() : string
    {
        return 'mixed';
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
    public function isValid($value) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function cast($value)
    {
        return $value;
    }
}
