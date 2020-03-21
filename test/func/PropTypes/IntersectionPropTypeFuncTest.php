<?php

namespace Dhii\Structs\Tests\Func\PropTypes;

use ArrayAccess;
use ArrayIterator;
use ArrayObject;
use CachingIterator;
use Countable;
use Dhii\Structs\PropTypes\IntersectionPropType;
use Dhii\Structs\PropTypes\ObjectPropType;
use Exception;
use LimitIterator;
use PHPUnit\Framework\TestCase;
use stdClass;
use Traversable;

class IntersectionPropTypeFuncTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testValid()
    {
        $subject = new IntersectionPropType([
            new ObjectPropType(ArrayAccess::class),
            new ObjectPropType(Countable::class),
            new ObjectPropType(Traversable::class),
        ]);

        static::assertTrue($subject->isValid(new ArrayObject()));
        static::assertTrue($subject->isValid(new ArrayIterator()));
    }

    /**
     * @since [*next-version*]
     */
    public function testValidFail()
    {
        $subject = new IntersectionPropType([
            new ObjectPropType(ArrayAccess::class),
            new ObjectPropType(Countable::class),
            new ObjectPropType(Traversable::class),
        ]);

        // Iterable, but not countable or array access
        static::assertFalse($subject->isValid(new LimitIterator(new ArrayIterator())));
    }

    /**
     * @since [*next-version*]
     */
    public function testValidFailOthers()
    {
        $subject = new IntersectionPropType([
            new ObjectPropType(ArrayAccess::class),
            new ObjectPropType(Countable::class),
            new ObjectPropType(Traversable::class),
        ]);

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
