<?php

namespace Dhii\Structs\Tests\Stubs;

use Dhii\Structs\PropType;
use PHPUnit\Framework\Constraint\IsEqual;

/**
 * A mock prop type.
 *
 * @since [*next-version*]
 */
class MockPropType implements PropType
{
    /**
     * The value the mock will return when casting, if {@link isNoop} is false and {@link function} is null.
     *
     * @var mixed|null
     */
    protected $return = null;

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
     *
     */
    public function cast($value)
    {
        if ($this->arg !== null && $this->arg !== $value) {
            $constraint = new IsEqual($this->arg);
            $constraint->evaluate($value, 'Argument does not match expected value');
        }

        if ($this->isNoop) {
            return $value;
        }

        if ($this->function !== null) {
            return ($this->function)($value);
        }

        return $this->return;
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
        return true;
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
     * @param mixed $value The value to return.
     *
     * @return $this
     */
    public function willReturn($value)
    {
        $this->isNoop = false;
        $this->function = null;
        $this->return = $value;

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
        $this->return = null;
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
}
