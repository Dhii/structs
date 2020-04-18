<?php

namespace Dhii\Structs;

use BadMethodCallException;
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
 * Lastly, the class reverses some property names for internal use. These properties use double underscore prefixed
 * names so as to reduce the probability of conflicting with any consumer struct property names. Any additional
 * internal properties that may be added in the future will also be prefixed with double underscores. Therefore, it is
 * highly recommended to avoid using double underscore prefixes in property names; this should ensure that your struct
 * properties will never conflict.
 *
 * @since [*next-version*]
 */
abstract class Struct implements Serializable
{
    /**
     * A cache of the prop types.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected static $__propTypesCache = null;

    /**
     * The struct's property data map.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $__data = [];

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param array $data An associative array that maps property names to their corresponding values.
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
     * A static version of the constructor.
     *
     * @since [*next-version*]
     *
     * @param array $data An associative array that maps property names to their corresponding values.
     *
     * @return static The created struct instance.
     */
    public static function fromArray(array $data = [])
    {
        return new static($data);
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
     * Functionally equivalent to {@link toArray()}, but called when a struct instance is passed to {@link var_dump()}.
     *
     * @since [*next-version*]
     *
     * @return array An associative array that maps the property names to their current values.
     */
    public function __debugInfo()
    {
        return static::toArray($this);
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
     * Instance-specific variant of {@link propTypes()}, which caches the prop types on a per-class basis to save
     * time and memory.
     *
     * @since [*next-version*]
     *
     * @return PropType[] A map of property names to their corresponding types.
     */
    protected function getPropTypes()
    {
        if (static::$__propTypesCache === null) {
            static::$__propTypesCache = static::propTypes();
        }

        return static::$__propTypesCache;
    }

    /**
     * Derives a new copy of struct instance with some modifications to its property values.
     *
     * @since [*next-version*]
     *
     * @param Struct $struct  The struct to derive from.
     * @param array  $changes An associative array that maps property names to their new values.
     *
     * @return static The copied struct with the applied changes. If the $changes parameter is empty, no copy is
     *                performed and the same instance is returned.
     */
    public static function derive(Struct $struct, array $changes)
    {
        if (empty($changes)) {
            return $struct;
        }

        $clone = clone $struct;

        $props = $struct->getPropTypes();
        foreach ($changes as $key => $value) {
            if (!isset($props[$key])) {
                throw static::createUndefinedPropException($key);
            }

            $clone->__data[$key] = $props[$key]->cast($value);
        }

        return $clone;
    }

    /**
     * Checks if the given structs have equal state.
     *
     * This method compares the structs using loose value equivalence (==).
     *
     * @since [*next-version*]
     *
     * @param Struct[] $structs The structs to compare.
     *
     * @return bool True if all the given structs have equal states, false if at least one is different.
     */
    public static function areEqual(Struct...$structs)
    {
        if (count($structs) < 2) {
            throw new BadMethodCallException('You must provide at least 2 structs to compare');
        }

        $first = reset($structs);
        while ($second = next($structs)) {
            if ($first->__data != $second->__data || $first->getPropTypes() != $second->getPropTypes()) {
                return false;
            }

            $first = $second;
        }

        return true;
    }

    /**
     * Exports a struct instance to a native associative array.
     *
     * @since [*next-version*]
     *
     * @param Struct $struct The struct instance to export.
     *
     * @return array An associative array that maps the property names to their current values.
     */
    public static function toArray(Struct $struct)
    {
        return $struct->__data;
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
    abstract static public function propTypes() : array;
}
