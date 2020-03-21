<?php

namespace Dhii\Structs\Tests\Unit\PropsTypes;

use Dhii\Structs\PropTypes\CustomPropType;
use Dhii\Structs\Tests\Stubs\FunctionStub;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use TypeError;

/**
 * @since [*next-version*]
 */
class CustomPropTypeUnitTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testGetName()
    {
        $name = uniqid('name');
        $subject = new CustomPropType($name, function () {
        });

        static::assertEquals($name, $subject->getName());
    }

    /**
     * @since [*next-version*]
     */
    public function testGetDefault()
    {
        $default = uniqid('$default');
        $subject = new CustomPropType(uniqid(), function () {
        }, $default);

        static::assertEquals($default, $subject->getDefault());
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testCast()
    {
        $value = uniqid('value');
        $expected = uniqid('casted');

        /* @var $castFn MockObject|callable */
        $castFn = $this->getMockForAbstractClass(FunctionStub::class);
        $castFn->expects(static::once())->method('__invoke')->with($value)->willReturn($expected);

        $subject = new CustomPropType('custom', $castFn);
        $actual = $subject->cast($value);

        static::assertSame($expected, $actual);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testIsValid()
    {
        $value = uniqid('value');
        $casted = uniqid('casted');

        /* @var $castFn MockObject|callable */
        $castFn = $this->getMockForAbstractClass(FunctionStub::class);
        $castFn->expects(static::once())->method('__invoke')->with($value)->willReturn($casted);

        $subject = new CustomPropType('custom', $castFn);

        static::assertTrue($subject->isValid($value));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testIsValidFail()
    {
        $value = uniqid('value');

        /* @var $castFn MockObject|callable */
        $castFn = $this->getMockForAbstractClass(FunctionStub::class);
        $castFn->expects(static::once())->method('__invoke')->with($value)->willThrowException(new TypeError());

        $subject = new CustomPropType('custom', $castFn);

        static::assertFalse($subject->isValid($value));
    }
}
