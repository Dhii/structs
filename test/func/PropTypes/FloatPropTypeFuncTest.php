<?php

namespace Dhii\Structs\Tests\Func\PropTypes;

use ArrayIterator;
use ArrayObject;
use Dhii\Structs\PropTypes\FloatPropType;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

class FloatPropTypeFuncTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testValid()
    {
        $subject = new FloatPropType();

        static::assertFalse($subject->isValid(null));
        static::assertTrue($subject->isValid(true));
        static::assertTrue($subject->isValid(false));
        static::assertTrue($subject->isValid(0));
        static::assertTrue($subject->isValid(0.0));
        static::assertTrue($subject->isValid(123456));
        static::assertTrue($subject->isValid(123.456));
        static::assertFalse($subject->isValid(""));
        static::assertTrue($subject->isValid("123"));
        static::assertTrue($subject->isValid("123.456"));
        static::assertFalse($subject->isValid("foobar"));
        static::assertFalse($subject->isValid(new Exception()));
        static::assertFalse($subject->isValid([]));
        static::assertFalse($subject->isValid(['test', 'foo', 'bar', 'baz']));
        static::assertFalse($subject->isValid(['test' => 'foo', 'bar' => 'baz']));
        static::assertFalse($subject->isValid(new stdClass()));
        static::assertFalse($subject->isValid(new ArrayObject()));
        static::assertFalse($subject->isValid(new ArrayIterator()));
        static::assertFalse($subject->isValid('substr'));
        static::assertFalse($subject->isValid('DateTime::createFromFormat'));
        static::assertFalse($subject->isValid([$this, 'testGetName']));
        static::assertFalse($subject->isValid(function () {
            // ...
        }));
    }
}
