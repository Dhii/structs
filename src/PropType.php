<?php

namespace Dhii\Structs;

use TypeError;

/**
 * Represents a struct property type.
 *
 * A property type defines how a struct property value is validated and casted. Implementations should make an effort to
 * mimic PHP's type validation and casting behavior as closely as possible, even if intuition says otherwise. This will
 * help maintain consistency across PHP's native type system and the struct user-land type system.
 *
 * @since [*next-version*]
 */
interface PropType
{
    /**
     * Retrieves the human-friendly name of the type.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Retrieves the default value for this type.
     *
     * @since [*next-version*]
     *
     * @return mixed
     */
    public function getDefault();

    /**
     * Casts a given value into this type.
     *
     * @since [*next-version*]
     *
     * @param mixed $value The value to cast.
     *
     * @return mixed The casted value.
     *
     * @throws TypeError If the value is invalid and cannot be cast.
     */
    public function cast($value);

    /**
     * Validates whether a value is acceptable for this type or not.
     *
     * @since [*next-version*]
     *
     * @param mixed $value The value to validate.
     *
     * @return bool True if the value is valid, false if not.
     */
    public function isValid($value) : bool;
}
