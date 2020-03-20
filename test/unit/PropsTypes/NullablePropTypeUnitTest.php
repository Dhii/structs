<?php

namespace Dhii\Structs\Tests\Unit\PropsTypes;

use Dhii\Structs\PropType;
use Dhii\Structs\PropTypes\NullablePropType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use TypeError;

/**
 * @since [*next-version*]
 */
class NullablePropTypeUnitTest extends TestCase
{
    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testGetName()
    {
        /* @var $inner MockObject&PropType */
        $inner = $this->getMockForAbstractClass(PropType::class);

        $inner->method('getName')->willReturn('Foo');

        $subject = new NullablePropType($inner);
        $expected = '?Foo';

        static::assertEquals($expected, $subject->getName());
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testGetDefault()
    {
        /* @var $inner MockObject&PropType */
        $inner = $this->getMockForAbstractClass(PropType::class);

        $subject = new NullablePropType($inner);

        static::assertNull($subject->getDefault());
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testValidNullInnerTrue()
    {
        /* @var $inner MockObject&PropType */
        $inner = $this->getMockForAbstractClass(PropType::class);
        $inner->expects(static::never())->method('isValid')->willReturn(true);

        $subject = new NullablePropType($inner);
        $input = null;

        static::assertTrue($subject->isValid($input));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testValidNullInnerFalse()
    {
        /* @var $inner MockObject&PropType */
        $inner = $this->getMockForAbstractClass(PropType::class);
        $inner->expects(static::never())->method('isValid')->willReturn(false);

        $subject = new NullablePropType($inner);
        $input = null;

        static::assertTrue($subject->isValid($input));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testValidInnerTrue()
    {
        /* @var $inner MockObject&PropType */
        $inner = $this->getMockForAbstractClass(PropType::class);
        $inner->expects(static::once())->method('isValid')->willReturn(true);

        $subject = new NullablePropType($inner);
        $input = uniqid('input');

        static::assertTrue($subject->isValid($input));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testValidInnerFalse()
    {
        /* @var $inner MockObject&PropType */
        $inner = $this->getMockForAbstractClass(PropType::class);
        $inner->expects(static::once())->method('isValid')->willReturn(false);

        $subject = new NullablePropType($inner);
        $input = uniqid('input');

        static::assertFalse($subject->isValid($input));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testCast()
    {
        $expected = uniqid('output');

        /* @var $inner MockObject&PropType */
        $inner = $this->getMockForAbstractClass(PropType::class);
        $inner->expects(static::once())->method('cast')->willReturn($expected);

        $subject = new NullablePropType($inner);
        $input = uniqid('input');
        $output = $subject->cast($input);

        static::assertSame($expected, $output);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testCastNull()
    {
        $expected = uniqid('output');

        /* @var $inner MockObject&PropType */
        $inner = $this->getMockForAbstractClass(PropType::class);
        $inner->expects(static::never())->method('cast')->willReturn($expected);

        $subject = new NullablePropType($inner);
        $output = $subject->cast(null);

        static::assertNull($output);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testCastFail()
    {
        /* @var $inner MockObject&PropType */
        $inner = $this->getMockForAbstractClass(PropType::class);
        $inner->expects(static::once())->method('cast')->willThrowException(new TypeError());

        $subject = new NullablePropType($inner);
        $input = uniqid('input');

        $this->expectException(TypeError::class);
        $subject->cast($input);
    }
}
