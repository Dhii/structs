<?php

namespace Dhii\Structs\PropTypes;

use Dhii\Structs\PropType;
use Dhii\Structs\Ty;
use LogicException;

/**
 * Enum struct property type.
 *
 * This type allows you to type-hint struct properties such that values are restricted to a particular set of
 * pre-defined acceptable values.
 *
 * @since [*next-version*]
 */
class EnumPropType implements PropType
{
    /**
     * @since [*next-version*]
     *
     * @var array
     */
    protected $values;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (empty($values)) {
            throw new LogicException('Enum value set cannot be empty');
        }

        $this->values = $values;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getName() : string
    {
        $list = implode(', ', array_map('strval', $this->values));

        return "{{$list}}";
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getDefault()
    {
        return reset($this->values);
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

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function isValid($value) : bool
    {
        return in_array($value, $this->values, true);
    }
}
