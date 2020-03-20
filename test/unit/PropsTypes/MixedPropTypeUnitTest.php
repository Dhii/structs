<?php

namespace Dhii\Structs\Tests\Unit\PropsTypes;

use Dhii\Structs\PropTypes\MixedPropType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @since [*next-version*]
 */
class MixedPropTypeUnitTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testGetName()
    {
        $subject = new MixedPropType();

        static::assertEquals('mixed', $subject->getName());
    }

    /**
     * @since [*next-version*]
     */
    public function testGetDefault()
    {
        $subject = new MixedPropType();

        static::assertNull($subject->getDefault());
    }

    /**
     * @since [*next-version*]
     */
    public function testCast()
    {
        /* @var $subject MockObject&MixedPropType */
        $subject = $this->getMockBuilder(MixedPropType::class)->setMethods(['isValid'])->getMock();
        $subject->expects(static::never())->method('isValid');

        $input = uniqid('input');
        $output = $subject->cast($input);

        static::assertSame($input, $output);
    }
}
