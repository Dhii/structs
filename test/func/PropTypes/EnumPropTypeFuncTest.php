<?php

namespace Dhii\Structs\Tests\Func\PropTypes;

use Dhii\Structs\PropTypes\EnumPropType;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @since [*next-version*]
 */
class EnumPropTypeFuncTest extends TestCase
{
    /**
     * @since [*next-version*]`
     */
    public function testCreateIntegers()
    {
        $subject = new EnumPropType([1, 5, 9, 1585]);

        static::assertEquals('{1, 5, 9, 1585}', $subject->getName());
        static::assertEquals(1, $subject->getDefault());
    }

    /**
     * @since [*next-version*]
     */
    public function testCreateFloats()
    {
        $subject = new EnumPropType([3.14159, 1.41421, 0.69314, 1.61803, 2.71828]);

        static::assertEquals('{3.14159, 1.41421, 0.69314, 1.61803, 2.71828}', $subject->getName());
        static::assertEquals(3.14159, $subject->getDefault());
    }

    /**
     * @since [*next-version*]
     */
    public function testCreateStrings()
    {
        $subject = new EnumPropType(['foo', 'bar', 'baz']);

        static::assertEquals('{foo, bar, baz}', $subject->getName());
        static::assertEquals('foo', $subject->getDefault());
    }

    /**
     * @since [*next-version*]
     */
    public function testValid()
    {
        $subject = new EnumPropType([123456, 'foobar', 0.0]);

        static::assertFalse($subject->isValid(null));
        static::assertFalse($subject->isValid(true));
        static::assertFalse($subject->isValid(false));
        static::assertFalse($subject->isValid(0));
        static::assertTrue($subject->isValid(0.0));
        static::assertTrue($subject->isValid(123456));
        static::assertFalse($subject->isValid(123.456));
        static::assertFalse($subject->isValid(""));
        static::assertFalse($subject->isValid("123"));
        static::assertFalse($subject->isValid("123.456"));
        static::assertTrue($subject->isValid("foobar"));
        static::assertFalse($subject->isValid([]));
        static::assertFalse($subject->isValid(['test', 'foo', 'bar', 'baz']));
        static::assertFalse($subject->isValid(['test' => 'foo', 'bar' => 'baz']));
        static::assertFalse($subject->isValid(new stdClass()));
        static::assertFalse($subject->isValid('substr'));
        static::assertFalse($subject->isValid('DateTime::createFromFormat'));
        static::assertFalse($subject->isValid([$this, 'testGetName']));
        static::assertFalse($subject->isValid(function () {
            // ...
        }));
    }
}
