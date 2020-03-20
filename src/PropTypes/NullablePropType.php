<?php

namespace Dhii\Structs\PropTypes;

use Dhii\Structs\PropType;

/**
 * Nullable struct property type.
 *
 * This type decorates another to allow null values in addition to any values allowed by the decorated type.
 *
 * @since [*next-version*]
 */
class NullablePropType implements PropType
{
    /**
     * @since [*next-version*]
     *
     * @var PropType
     */
    protected $type;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param PropType $type
     */
    public function __construct(PropType $type)
    {
        $this->type = $type;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getName() : string
    {
        return '?' . $this->type->getName();
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
        return is_null($value) || $this->type->isValid($value);
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function cast($value)
    {
        if (is_null($value)) {
            return null;
        }

        return $this->type->cast($value);
    }
}
