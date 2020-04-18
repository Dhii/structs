<?php

namespace Dhii\Structs\PropTypes;

use Dhii\Structs\PropType;
use Dhii\Structs\Ty;

/**
 * Array struct property type.
 *
 * Can optionally restrict the types of array elements.
 *
 * @since [*next-version*]
 */
class ArrayPropType implements PropType
{
    /**
     * @since [*next-version*]
     *
     * @var PropType|null
     */
    protected $elType;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param PropType|null $elType Optional type restriction for array elements.
     */
    public function __construct(?PropType $elType = null)
    {
        $this->elType = $elType;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getName() : string
    {
        return 'array';
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
        if (!is_array($value)) {
            return false;
        }

        if ($this->elType === null) {
            return true;
        }

        return array_reduce($value, function ($prev, $curr) {
            return $prev && $this->elType->isValid($curr);
        }, true);
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function cast($value)
    {
        if (!is_array($value)) {
            throw Ty::createTypeError($this, $value);
        }

        if ($this->elType === null) {
            return $value;
        }

        return array_map(function ($elem) {
            return $this->elType->cast($elem);
        }, $value);
    }
}
