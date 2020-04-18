<?php

namespace Dhii\Structs\Tests\Func\PropTypes;

use ArrayIterator;
use ArrayObject;
use Dhii\Structs\PropTypes\ArrayPropType;
use Dhii\Structs\Ty;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

class ArrayPropTypeFuncTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testGetName()
    {
        $subject = new ArrayPropType();

        static::assertEquals('array', $subject->getName());
    }

    /**
     * @since [*next-version*]
     */
    public function testGetDefault()
    {
        $subject = new ArrayPropType();

        static::assertEquals([], $subject->getDefault());
    }

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

    /**
     * @since [*next-version*]
     */
    public function testIsValidWithElType()
    {
        $subject = new ArrayPropType(Ty::string());

        $input = ['string1', 'string2', 'string3'];

        static::assertTrue($subject->isValid($input));
    }

    /**
     * @since [*next-version*]
     */
    public function testIsValidFailWithElType()
    {
        $subject = new ArrayPropType(Ty::string());

        $input = ['string1', 12563, 'string3'];

        static::assertTrue($subject->isValid($input));
    }

    /**
     * @since [*next-version*]
     */
    public function testCast()
    {
        $subject = new ArrayPropType();

        $input = ['test', 'value'];
        $output = $subject->cast($input);

        static::assertSame($output, $input);
    }

    /**
     * @since [*next-version*]
     */
    public function testCastFail()
    {
        $subject = new ArrayPropType();

        $input = uniqid('not-an-array');

        $this->expectException(TypeError::class);
        $subject->cast($input);
    }

    /**
     * @since [*next-version*]
     */
    public function testCastWithElType()
    {
        $subject = new ArrayPropType(Ty::string());

        $input = ['test', 'value', 5];
        $expected = ['test', 'value', '5'];
        $output = $subject->cast($input);

        static::assertSame($expected, $output);
    }

    /**
     * @since [*next-version*]
     */
    public function testCastFailWithElType()
    {
        $subject = new ArrayPropType(Ty::object());

        $input = [new stdClass(), 'not-an-object', new stdClass()];

        $this->expectException(TypeError::class);
        $subject->cast($input);
    }
}
