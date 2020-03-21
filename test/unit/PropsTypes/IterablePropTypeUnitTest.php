<?php

namespace Dhii\Structs\Tests\Unit\PropsTypes;

use Dhii\Structs\PropTypes\IterablePropType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @since [*next-version*]
 */
class IterablePropTypeUnitTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testGetName()
    {
        $subject = new IterablePropType();

        static::assertEquals('iterable', $subject->getName());
    }

    /**
     * @since [*next-version*]
     */
    public function testGetDefault()
    {
        $subject = new IterablePropType();

        static::assertEquals([], $subject->getDefault());
    }

    /**
     * @since [*next-version*]
     */
    public function testCast()
    {
        /* @var $subject MockObject&IterablePropType */
        $subject = $this->getMockBuilder(IterablePropType::class)->setMethods(['isValid'])->getMock();
        $subject->expects(static::once())->method('isValid')->willReturn(true);

        $input = uniqid('input');
        $output = $subject->cast($input);

        static::assertSame($output, $input);
    }

    /**
     * @since [*next-version*]
     */
    public function testCastFail()
    {
        /* @var $subject MockObject&IterablePropType */
        $subject = $this->getMockBuilder(IterablePropType::class)->setMethods(['isValid'])->getMock();
        $subject->expects(static::once())->method('isValid')->willReturn(false);

        $input = uniqid('input');

        $this->expectException(TypeError::class);
        $subject->cast($input);
    }
}
