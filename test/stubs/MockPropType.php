<?php

namespace Dhii\Structs\Tests\Stubs;

use Dhii\Structs\PropType;
use PHPUnit\Framework\Constraint\IsEqual;
use TypeError;

/**
 * A mock prop type.
 *
 * @since [*next-version*]
 */
class MockPropType implements PropType
{
    /**
     * The value the mock will return when casting, if {@link isNoop} is false and {@link function} is null.
     * If an instance of {@link TypeError}, it will be thrown when casting.
     *
     * @var mixed|TypeError|null
     */
    protected $cast = null;

    /**
     * The function to run when casting, if {@link $isNoop} is false.
     *
     * @var mixed|null
     */
    protected $function = null;

    /**
     * Whether the mock is a no-op; i.e. returns the argument when casting.
     *
     * @var bool
     */
    protected $isNoop = true;

    /**
     * The argument value to expect, if any.
     *
     * @var mixed|null
     */
    protected $arg = null;

    /**
     * The prop type's default value.
     *
     * @var mixed|null
     */
    protected $default = null;

    /**
     * The validity to return from {@link isValid}, or null.
     *
     * @var boolean|null
     */
    protected $validity = null;

    /**
     * Static constructor, for easily creating instances.
     *
     * @since [*next-version*]
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getName() : string
    {
        return '<mock>';
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function cast($value)
    {
        $this->checkArg($value);

        if ($this->isNoop) {
            return $value;
        }

        if ($this->function !== null) {
            return ($this->function)($value);
        }

        if ($this->cast instanceof TypeError) {
            throw $this->cast;
        }

        return $this->cast;
    }

    /**
     * @inheritDoc
     *
     * Unused by tests.
     *
     * @since [*next-version*]
     */
    public function isValid($value) : bool
    {
        $this->checkArg($value);

        return $this->validity;
    }

    /**
     * Checks an argument against the expectation constraint, if it's set.
     *
     * @since [*next-version*]
     *
     * @param mixed $arg The argument to check.
     */
    protected function checkArg($arg)
    {
        if ($this->arg !== null && $this->arg !== $arg) {
            $constraint = new IsEqual($this->arg);
            $constraint->evaluate($arg, 'Argument does not match expected value');
        }
    }

    /**
     * Makes the mock return the argument when casting.
     *
     * @since [*next-version*]
     *
     * @return $this
     */
    public function willReturnArg()
    {
        $this->isNoop = true;

        return $this;
    }

    /**
     * Makes the mock return a specific value when casting.
     *
     * @since [*next-version*]
     *
     * @param mixed|TypeError $value The value to return, or a {@link TypeError} to fail casting.
     *
     * @return $this
     */
    public function willReturn($value)
    {
        $this->isNoop = false;
        $this->function = null;
        $this->cast = $value;

        return $this;
    }

    /**
     * Shortcut for calling {@link willReturn()} with a {@link TypeError} instance.
     *
     * @since [*next-version*]
     *
     * @return $this
     */
    public function willThrow()
    {
        $this->isNoop = false;
        $this->function = null;
        $this->cast = new TypeError();

        return $this;
    }

    /**
     * Makes the mock run a specific function when casting.
     *
     * @since [*next-version*]
     *
     * @param mixed $function The function to run.
     *
     * @return $this
     */
    public function willDo(callable $function)
    {
        $this->isNoop = false;
        $this->cast = null;
        $this->function = $function;

        return $this;
    }

    /**
     * Makes the mock expect a specific argument value when casting.
     *
     * @since [*next-version*]
     *
     * @param mixed $arg The value to expect.
     *
     * @return $this
     */
    public function expects($arg)
    {
        $this->arg = $arg;

        return $this;
    }

    /**
     * Sets the mock's default value.
     *
     * @since [*next-version*]
     *
     * @param mixed $default The default value.
     *
     * @return $this
     */
    public function defaultsTo($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Sets the mock's validity return value for {@link isValid()}.
     *
     * @since [*next-version*]
     *
     * @param bool $validity The validity to return.
     *
     * @return $this
     */
    public function validatesTo(bool $validity)
    {
        $this->validity = $validity;

        return $this;
    }
}
