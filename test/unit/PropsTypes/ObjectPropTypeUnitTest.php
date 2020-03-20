<?php

namespace Dhii\Structs\Tests\Unit\PropsTypes;

use DateTime;
use Dhii\Structs\PropTypes\ObjectPropType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @since [*next-version*]
 */
class ObjectPropTypeUnitTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testGetName()
    {
        $subject = new ObjectPropType();

        static::assertEquals('object', $subject->getName());
    }

    /**
     * @since [*next-version*]
     */
    public function testGetNameWithClassName()
    {
        $className = DateTime::class;
        $subject = new ObjectPropType($className);

        static::assertEquals($className, $subject->getName());
    }

    /**
     * @since [*next-version*]
     */
    public function testGetDefault()
    {
        $subject = new ObjectPropType();

        static::assertNull($subject->getDefault());
    }

    /**
     * @since [*next-version*]
     */
    public function testCast()
    {
        /* @var $subject MockObject&ObjectPropType */
        $subject = $this->getMockBuilder(ObjectPropType::class)->setMethods(['isValid'])->getMock();
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
        /* @var $subject MockObject&ObjectPropType */
        $subject = $this->getMockBuilder(ObjectPropType::class)->setMethods(['isValid'])->getMock();
        $subject->expects(static::once())->method('isValid')->willReturn(false);

        $input = uniqid('input');

        $this->expectException(TypeError::class);
        $subject->cast($input);
    }
}
