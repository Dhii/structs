<?php

namespace Dhii\Structs\Tests\Unit\PropsTypes;

use Dhii\Structs\PropTypes\EnumPropType;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @since [*next-version*]
 */
class EnumPropTypeUnitTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testCreateEmptySet()
    {
        $this->expectException(LogicException::class);

        new EnumPropType([]);
    }

    /**
     * @since [*next-version*]
     */
    public function testCast()
    {
        /* @var $subject MockObject&EnumPropType */
        $subject = $this->getMockBuilder(EnumPropType::class)
                        ->setConstructorArgs([['foo', 'bar']])
                        ->setMethods(['isValid'])
                        ->getMock();
        $subject->expects(static::once())->method('isValid')->willReturn(true);

        $input = uniqid('input');
        $output = $subject->cast($input);

        static::assertSame($input, $output);
    }

    /**
     * @since [*next-version*]
     */
    public function testCastFail()
    {
        /* @var $subject MockObject&EnumPropType */
        $subject = $this->getMockBuilder(EnumPropType::class)
                        ->setConstructorArgs([['foo', 'bar']])
                        ->setMethods(['isValid'])
                        ->getMock();
        $subject->expects(static::once())->method('isValid')->willReturn(false);

        $input = uniqid('input');

        $this->expectException(TypeError::class);
        $subject->cast($input);
    }
}
