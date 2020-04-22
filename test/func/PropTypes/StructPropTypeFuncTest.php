<?php

namespace Dhii\Structs\Tests\Func\PropTypes;

use ArrayIterator;
use ArrayObject;
use Dhii\Structs\PropTypes\StructPropType;
use Dhii\Structs\Struct;
use Dhii\Structs\Tests\Stubs\MockPropType;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @since [*next-version*]
 */
class StructPropTypeFuncTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testValid()
    {
        $struct = new class() extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->validatesTo(true),
                    'bar' => MockPropType::create()->validatesTo(true),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct));

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
