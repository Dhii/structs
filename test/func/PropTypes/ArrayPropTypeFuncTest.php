<?php

namespace Dhii\Structs\Tests\Func\PropTypes;

use ArrayIterator;
use ArrayObject;
use Dhii\Structs\PropTypes\ArrayPropType;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

class ArrayPropTypeFuncTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testValid()
    {
        $subject = new ArrayPropType();

        static::assertFalse($subject->isValid(null));
        static::assertFalse($subject->isValid(true));
        static::assertFalse($subject->isValid(false));
        static::assertFalse($subject->isValid(0));
        static::assertFalse($subject->isValid(0.0));
        static::assertFalse($subject->isValid(123456));
        static::assertFalse($subject->isValid(123.456));
        static::assertFalse($subject->isValid(""));
        static::assertFalse($subject->isValid("123"));
        static::assertFalse($subject->isValid("123.456"));
        static::assertFalse($subject->isValid("foobar"));
        static::assertFalse($subject->isValid(new Exception()));
        static::assertTrue($subject->isValid([]));
        static::assertTrue($subject->isValid(['test', 'foo', 'bar', 'baz']));
        static::assertTrue($subject->isValid(['test' => 'foo', 'bar' => 'baz']));
        static::assertFalse($subject->isValid(new stdClass()));
        static::assertFalse($subject->isValid(new ArrayObject()));
        static::assertFalse($subject->isValid(new ArrayIterator()));
        static::assertFalse($subject->isValid('substr'));
        static::assertFalse($subject->isValid('DateTime::createFromFormat'));
        static::assertTrue($subject->isValid([$this, 'testGetName']));
        static::assertFalse($subject->isValid(function () {
            // ...
        }));
    }
}
