<?php

namespace Dhii\Structs\Tests\Unit\PropsTypes;

use Dhii\Structs\PropTypes\StringPropType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @since [*next-version*]
 */
class StringPropTypeUnitTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testGetName()
    {
        $subject = new StringPropType();

        static::assertEquals('string', $subject->getName());
    }

    /**
     * @since [*next-version*]
     */
    public function testGetDefault()
    {
        $subject = new StringPropType();

        static::assertEquals('', $subject->getDefault());
    }

    /**
     * @since [*next-version*]
     */
    public function testCast()
    {
        /* @var $subject MockObject&StringPropType */
        $subject = $this->getMockBuilder(StringPropType::class)->setMethods(['isValid'])->getMock();
        $subject->expects(static::once())->method('isValid')->willReturn(true);

        $input = uniqid('input');
        $output = $subject->cast($input);

        static::assertIsString($output);
    }

    /**
     * @since [*next-version*]
     */
    public function testCastFail()
    {
        /* @var $subject MockObject&StringPropType */
        $subject = $this->getMockBuilder(StringPropType::class)->setMethods(['isValid'])->getMock();
        $subject->expects(static::once())->method('isValid')->willReturn(false);

        $input = uniqid('input');

        $this->expectException(TypeError::class);
        $subject->cast($input);
    }
}
