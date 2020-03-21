<?php

namespace Dhii\Structs;

use Exception;
use LogicException;
use Serializable;

/**
 * An implementation of an immutable struct.
 *
 * This class is intended to be extended.
 *
 * When extended, you must override the {@link getPropTypes} method to specify the property types for the struct.
 * Additionally, you can get IntelliSense auto-completion within your ID by adding a docblock to the extended class with
 * {@link https://manual.phpdoc.org/HTMLSmartyConverter/PHP/phpDocumentor/tutorial_tags.property.pkg.html property-read}
 * annotations.
 *
 * Struct instances are intended to be used as plain objects, similar to {@link stdClass}. The key difference is that
 * properties are type-safe, and instances are immutable. An instance can create a derived copy via {@link with()}
 * method, which leaves the original instance unchanged.
 *
 * **Important**: Struct properties may have any valid array key as their name. However, be wary that PHP's object
 * property access syntax is limited in what properties may be fetched. To read properties whose names would lead to
 * invalid PHP syntax, use curly braces around a quoted property name:
 *
 * Example: `$struct->{"bob page"}`
 *
 * Lastly, this class only reserves one property name: "__data". The property is prefixed with a double underscore to
 * reduce the probability of conflicting with any consumer property name. However, it is recommended to avoid to using
 * double underscore prefixes as additional properties may be added in future versions to accommodate new features.
 *
 * @since [*next-version*]
 */
abstract class Struct implements Serializable
{
    /**
     * @since [*next-version*]
     *
     * @var array
     */
    protected $__data;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param array $data A mapping of property names to their values.
     */
    public function __construct(array $data = [])
    {
        $props = $this->getPropTypes();

        $this->__data = [];
        foreach ($props as $key => $type) {
            $this->__data[$key] = isset($data[$key])
                ? $type->cast($data[$key])
                : $type->getDefault();

            unset($data[$key]);
        }

        if (!empty($data)) {
            $class = static::class;
            $props = implode(', ', array_keys($data));
            throw new LogicException("Struct {$class} does not have the following properties: {$props}");
        }
    }

    /**
     * Retrieves the value for a property.
     *
     * @since [*next-version*]
     *
     * @param string $name The name of the property.
     *
     * @return mixed|null The value of the property.
     */
    public function __get($name)
    {
        $props = $this->getPropTypes();

        if (!array_key_exists($name, $props)) {
            throw static::createUndefinedPropException($name);
        }

        return $this->__data[$name];
    }

    /**
     * Should not be used. Only exists to warn developers of misuse.
     *
     * @since [*next-version*]
     *
     * @param string $name  The name of the property.
     * @param mixed  $value The value being assigned to the property.
     *
     * @throws Exception Always throws.
     */
    public function __set($name, $value)
    {
        throw new LogicException('Cannot set property values. Use with() instead');
    }

    /**
     * Creates a copy of the struct instance with some modified properties.
     *
     * @since [*next-version*]
     *
     * @param array $changes An associative array that maps property names to their new values.
     *
     * @return Struct The copied struct with the applied changes. If the $changes parameter is empty, no copy is
     *                performed and the same instance is returned.
     */
    public function with(array $changes)
    {
        if (empty($changes)) {
            return $this;
        }

        $clone = clone $this;

        $props = $this->getPropTypes();
        foreach ($changes as $key => $value) {
            if (!isset($props[$key])) {
                throw static::createUndefinedPropException($key);
            }

            $clone->__data[$key] = $props[$key]->cast($value);
        }

        return $clone;
    }

    /**
     * Checks if two structs have equal state.
     *
     * This method compares the two structs using loose value equivalence (==).
     *
     * @since [*next-version*]
     *
     * @param Struct $struct The struct to compare to.
     *
     * @return bool True if the two structs have equal states, false otherwise.
     */
    public function isEqualTo(Struct $struct)
    {
        $thisPropsTypes = $this->getPropTypes();
        $otherPropTypes = $struct->getPropTypes();

        return $this->__data == $struct->__data && $thisPropsTypes == $otherPropTypes;
    }

    /**
     * Exports the struct instance to a native associative array.
     *
     * @since [*next-version*]
     *
     * @return array An associative array that maps the property names to their current values.
     */
    public function toArray()
    {
        return $this->__data;
    }

    /**
     * Functionally equivalent to {@link export()}, but called when a struct instance is passed to {@link var_dump()}.
     *
     * @since [*next-version*]
     *
     * @return array An associative array that maps the property names to their current values.
     */
    public function __debugInfo()
    {
        return $this->__data;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function serialize()
    {
        return serialize($this->__data);
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function unserialize($serialized)
    {
        $this->__data = unserialize($serialized);
    }

    /**
     * Creates an undefined property exception.
     *
     * @since [*next-version*]
     *
     * @param string $prop The property name.
     *
     * @return LogicException The created exception.
     */
    protected static function createUndefinedPropException(string $prop)
    {
        $struct = static::class;

        return new LogicException("Undefined property {$prop} for struct {$struct}");
    }

    /**
     * Retrieves the property types for this struct type.
     *
     * @since [*next-version*]
     *
     * @return PropType[] A map of property names to their corresponding types.
     */
    abstract public function getPropTypes() : array;
}
