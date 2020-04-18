<?php

namespace Dhii\Structs;

use ArrayAccess;
use Dhii\Structs\PropTypes\ArrayPropType;
use Dhii\Structs\PropTypes\BoolPropType;
use Dhii\Structs\PropTypes\CallablePropType;
use Dhii\Structs\PropTypes\CustomPropType;
use Dhii\Structs\PropTypes\EnumPropType;
use Dhii\Structs\PropTypes\FloatPropType;
use Dhii\Structs\PropTypes\IntersectionPropType;
use Dhii\Structs\PropTypes\IntPropType;
use Dhii\Structs\PropTypes\IterablePropType;
use Dhii\Structs\PropTypes\NullablePropType;
use Dhii\Structs\PropTypes\ObjectPropType;
use Dhii\Structs\PropTypes\StringPropType;
use Dhii\Structs\PropTypes\UnionPropType;
use TypeError;

/**
 * Helper methods for easily declaring property types.
 *
 * Some of the below methods use a static cache to conserve memory. Types that require or can accept arguments will not
 * have their instances cached.
 *
 * Also provides additional utility methods for property type implementations.
 *
 * @since [*next-version*]
 */
class Ty
{
    /**
     * Integer property type.
     *
     * @since [*next-version*]
     *
     * @return IntPropType
     */
    public static function int() : IntPropType
    {
        static $ty = null;
        is_null($ty) && $ty = new IntPropType();

        return $ty;
    }

    /**
     * Float property type.
     *
     * @since [*next-version*]
     *
     * @return FloatPropType
     */
    public static function float() : FloatPropType
    {
        static $ty = null;
        is_null($ty) && $ty = new FloatPropType();

        return $ty;
    }

    /**
     * Boolean property type.
     *
     * @since [*next-version*]
     *
     * @return BoolPropType
     */
    public static function bool() : BoolPropType
    {
        static $ty = null;
        is_null($ty) && $ty = new BoolPropType();

        return $ty;
    }

    /**
     * String property type.
     *
     * @since [*next-version*]
     *
     * @return StringPropType
     */
    public static function string() : StringPropType
    {
        static $ty = null;
        is_null($ty) && $ty = new StringPropType();

        return $ty;
    }

    /**
     * Callable property type.
     *
     * @since [*next-version*]
     *
     * @return CallablePropType
     */
    public static function callable() : CallablePropType
    {
        static $ty = null;
        is_null($ty) && $ty = new CallablePropType();

        return $ty;
    }

    /**
     * Array property type.
     *
     * @since [*next-version*]
     *
     * @return ArrayPropType
     */
    public static function array() : ArrayPropType
    {
        static $ty = null;
        is_null($ty) && $ty = new ArrayPropType();

        return $ty;
    }

    /**
     * Array property type.
     *
     * @since [*next-version*]
     *
     * @param PropType $ty type for array elements.
     *
     * @return ArrayPropType
     */
    public static function arrayOf(PropType $ty) : ArrayPropType
    {
        return new ArrayPropType($ty);
    }

    /**
     * Array-like property type. Shorthand for a union of array and {@link ArrayAccess}.
     *
     * @since [*next-version*]
     *
     * @return UnionPropType
     */
    public static function arrayLike() : UnionPropType
    {
        static $ty = null;
        is_null($ty) && $ty = Ty::union(Ty::array(), Ty::object(ArrayAccess::class));

        return $ty;
    }

    /**
     * Iterable property type.
     *
     * @since [*next-version*]
     *
     * @return IterablePropType
     */
    public static function iterable() : IterablePropType
    {
        static $ty = null;
        is_null($ty) && $ty = new IterablePropType();

        return $ty;
    }

    /**
     * Object property type.
     *
     * @since [*next-version*]
     *
     * @param string ...$what Optional list of classes or interfaces that objects must extend or implement.
     *
     * @return PropType
     */
    public static function object(string ...$what) : PropType
    {
        if (count($what) > 1) {
            $parents = array_map([Ty::class, 'object'], $what);

            return new IntersectionPropType($parents);
        }

        $parent = !empty($what)
            ? reset($what)
            : null;

        return new ObjectPropType($parent);
    }

    /**
     * Enum property type.
     *
     * @since [*next-version*]
     *
     * @param string ...$values The enum values. Should not be empty.
     *
     * @return EnumPropType
     */
    public static function enum(...$values)
    {
        return new EnumPropType($values);
    }

    /**
     * Type-union property type.
     *
     * @since [*next-version*]
     *
     * @param PropType ...$types The union types.
     *
     * @return UnionPropType
     */
    public static function union(PropType ...$types)
    {
        return new UnionPropType($types);
    }

    /**
     * Type-intersection property type.
     *
     * @since [*next-version*]
     *
     * @param PropType ...$types The intersection types.
     *
     * @return IntersectionPropType
     */
    public static function intersect(PropType ...$types)
    {
        return new IntersectionPropType($types);
    }

    /**
     * Nullable property type.
     *
     * @since [*next-version*]
     *
     * @param PropType $type The nullable type.
     *
     * @return NullablePropType
     */
    public static function nullable(PropType $type)
    {
        return new NullablePropType($type);
    }

    /**
     * Custom property type.
     *
     * @since [*next-version*]
     *
     * @param callable $castFn  A function that accepts a value as argument and should return the casted value. If the
     *                          argument value is not of an acceptable type, a {@link TypeError} should be thrown.
     * @param mixed    $default The default value, to use when a property of this type is uninitialized.
     *
     * @return CustomPropType
     */
    public static function custom(callable $castFn, $default = null)
    {
        return new CustomPropType('<custom>', $castFn, $default);
    }

    /**
     * Retrieves the name of the type for a given value.
     *
     * @since [*next-version*]
     *
     * @param mixed $value The value.
     *
     * @return string the name of the type.
     */
    public static function getTypeName($value)
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }

    /**
     * Creates a type error for invalid struct property values.
     *
     * @since [*next-version*]
     *
     * @param PropType $type  The property type.
     * @param mixed    $value The value.
     *
     * @return TypeError The created type error.
     */
    public static function createTypeError(PropType $type, $value) : TypeError
    {
        $typeName = $type->getName();
        $valType = static::getTypeName($value);

        return new TypeError("Property value must be of type {$typeName}, {$valType} given");
    }
}
