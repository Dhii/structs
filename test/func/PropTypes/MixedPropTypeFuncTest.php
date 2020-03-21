<?php

namespace Dhii\Structs\Tests\Func\PropTypes;

use ArrayIterator;
use ArrayObject;
use Dhii\Structs\PropTypes\MixedPropType;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

class MixedPropTypeFuncTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testValid()
    {
        $subject = new MixedPropType();

        static::assertTrue($subject->isValid(null));
        static::assertTrue($subject->isValid(true));
        static::assertTrue($subject->isValid(false));
        static::assertTrue($subject->isValid(0));
        static::assertTrue($subject->isValid(0.0));
        static::assertTrue($subject->isValid(123456));
        static::assertTrue($subject->isValid(123.456));
        static::assertTrue($subject->isValid(""));
        static::assertTrue($subject->isValid("123"));
        static::assertTrue($subject->isValid("123.456"));
        static::assertTrue($subject->isValid("foobar"));
        static::assertTrue($subject->isValid(new Exception()));
        static::assertTrue($subject->isValid([]));
        static::assertTrue($subject->isValid(['test', 'foo', 'bar', 'baz']));
        static::assertTrue($subject->isValid(['test' => 'foo', 'bar' => 'baz']));
        static::assertTrue($subject->isValid(new stdClass()));
        static::assertTrue($subject->isValid(new ArrayObject()));
        static::assertTrue($subject->isValid(new ArrayIterator()));
        static::assertTrue($subject->isValid('substr'));
        static::assertTrue($subject->isValid('DateTime::createFromFormat'));
        static::assertTrue($subject->isValid([$this, 'testGetName']));
        static::assertTrue($subject->isValid(function () {
            // ...
        }));
    }
}
