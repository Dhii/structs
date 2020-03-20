<?php

namespace Dhii\Structs\Tests\Unit\PropsTypes;

use Dhii\Structs\PropTypes\FloatPropType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @since [*next-version*]
 */
class FloatPropTypeUnitTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testGetName()
    {
        $subject = new FloatPropType();

        static::assertEquals('float', $subject->getName());
    }

    /**
     * @since [*next-version*]
     */
    public function testGetDefault()
    {
        $subject = new FloatPropType();

        static::assertEquals(0.0, $subject->getDefault());
    }

    /**
     * @since [*next-version*]
     */
    public function testCast()
    {
        /* @var $subject MockObject&FloatPropType */
        $subject = $this->getMockBuilder(FloatPropType::class)->setMethods(['isValid'])->getMock();
        $subject->expects(static::once())->method('isValid')->willReturn(true);

        $input = uniqid('input');
        $output = $subject->cast($input);

        static::assertIsFloat($output);
    }

    /**
     * @since [*next-version*]
     */
    public function testCastFail()
    {
        /* @var $subject MockObject&FloatPropType */
        $subject = $this->getMockBuilder(FloatPropType::class)->setMethods(['isValid'])->getMock();
        $subject->expects(static::once())->method('isValid')->willReturn(false);

        $input = uniqid('input');

        $this->expectException(TypeError::class);
        $subject->cast($input);
    }
}
