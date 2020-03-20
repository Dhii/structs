<?php

namespace Dhii\Structs\Tests\Unit\PropsTypes;

use Dhii\Structs\PropType;
use Dhii\Structs\PropTypes\IntersectionPropType;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use TypeError;

/**
 * @since [*next-version*]
 */
class IntersectionPropTypeUnitTest extends TestCase
{
    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testGetName()
    {
        $inner1 = $this->getMockForAbstractClass(PropType::class);
        $inner2 = $this->getMockForAbstractClass(PropType::class);
        $inner3 = $this->getMockForAbstractClass(PropType::class);

        $inner1->method('getName')->willReturn('Foo');
        $inner2->method('getName')->willReturn('Bar');
        $inner3->method('getName')->willReturn('Baz');

        $subject = new IntersectionPropType([$inner1, $inner2, $inner3]);
        $expected = 'Foo&Bar&Baz';

        static::assertEquals($expected, $subject->getName());
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testGetDefault()
    {
        $inner = $this->getMockForAbstractClass(PropType::class);
        $subject = new IntersectionPropType([$inner]);

        static::assertNull($subject->getDefault());
    }

    /**
     * @since [*next-version*]
     */
    public function testCreateEmptyList()
    {
        $this->expectException(LogicException::class);

        new IntersectionPropType([]);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testValid()
    {
        $inner1 = $this->getMockForAbstractClass(PropType::class);
        $inner2 = $this->getMockForAbstractClass(PropType::class);
        $inner3 = $this->getMockForAbstractClass(PropType::class);

        $inner1->expects(static::once())->method('isValid')->willReturn(true);
        $inner2->expects(static::once())->method('isValid')->willReturn(true);
        $inner3->expects(static::once())->method('isValid')->willReturn(true);

        $subject = new IntersectionPropType([$inner1, $inner2, $inner3]);
        $input = uniqid('input');

        static::assertTrue($subject->isValid($input));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testValidFail()
    {
        $inner1 = $this->getMockForAbstractClass(PropType::class);
        $inner2 = $this->getMockForAbstractClass(PropType::class);
        $inner3 = $this->getMockForAbstractClass(PropType::class);

        $inner1->expects(static::once())->method('isValid')->willReturn(true);
        $inner2->expects(static::once())->method('isValid')->willReturn(false);
        $inner3->expects(static::never())->method('isValid')->willReturn(true);

        $subject = new IntersectionPropType([$inner1, $inner2, $inner3]);
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
        $inner = $this->getMockForAbstractClass(PropType::class);

        /* @var $subject MockObject&IntersectionPropType */
        $subject = $this->getMockBuilder(IntersectionPropType::class)
                        ->setConstructorArgs([[$inner]])
                        ->setMethods(['isValid'])
                        ->getMock();
        $subject->expects(static::once())->method('isValid')->willReturn(true);

        $input = uniqid('input');
        $output = $subject->cast($input);

        static::assertSame($input, $output);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testCastFail()
    {
        $inner = $this->getMockForAbstractClass(PropType::class);

        /* @var $subject MockObject&IntersectionPropType */
        $subject = $this->getMockBuilder(IntersectionPropType::class)
                        ->setConstructorArgs([[$inner]])
                        ->setMethods(['isValid'])
                        ->getMock();
        $subject->expects(static::once())->method('isValid')->willReturn(false);

        $input = uniqid('input');

        $this->expectException(TypeError::class);
        $subject->cast($input);
    }
}
