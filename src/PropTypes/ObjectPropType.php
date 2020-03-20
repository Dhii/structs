<?php

namespace Dhii\Structs\PropTypes;

use Dhii\Structs\PropType;
use Dhii\Structs\Ty;
use LogicException;

/**
 * Object struct property type.
 *
 * By default, properties with this type will restrict values to any PHP object. The object type can be restricted
 * further by specifying a class or interface name, in which case object values must also extend or implement the given
 * class or interface name, respectively.
 *
 * @since [*next-version*]
 */
class ObjectPropType implements PropType
{
    /**
     * @since [*next-version*]
     *
     * @var string|null
     */
    protected $className;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string|null $className Optional class name, or interface name, to restrict the type checking to.
     */
    public function __construct(string $className = null)
    {
        if ($className !== null && !class_exists($className) && !interface_exists($className)) {
            throw new LogicException("Undefined class {$className}");
        }

        $this->className = $className;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getName() : string
    {
        return $this->className === null ? 'object' : $this->className;
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
        return $this->className === null
            ? is_object($value)
            : $value instanceof $this->className;
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
