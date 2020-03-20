<?php

namespace Dhii\Structs\PropTypes;

use Dhii\Structs\PropType;
use Dhii\Structs\Ty;

/**
 * Union struct property type.
 *
 * This type allows you to type-hint struct properties against a list of types, such that the value must conform TO AT
 * LEAST ONE of the types in that list.
 *
 * @since [*next-version*]
 */
class UnionPropType implements PropType
{
    /**
     * @since [*next-version*]
     *
     * @var PropType[]
     */
    protected $types;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param PropType[] $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getName() : string
    {
        return implode('|', array_map(function (PropType $type) {
            return $type->getName();
        }, $this->types));
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
        foreach ($this->types as $type) {
            if ($type->isValid($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function cast($value)
    {
        if (!$this->isValid($value)) {
            throw Ty::createTypeError($this, $value);
        }

        return $value;
    }
}
