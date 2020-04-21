<?php

namespace Dhii\Structs\Tests\Unit;

use Dhii\Structs\Struct;
use Dhii\Structs\Tests\Stubs\MockPropType;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * @since [*next-version*]
 */
class StructTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testCreate()
    {
        $data = [
            'foo' => '123',
            'bar' => '456',
        ];

        $subject = new class($data) extends Struct {
            protected static function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willReturnArg(),
                    'bar' => MockPropType::create()->willReturnArg(),
                ];
            }
        };

        static::assertInstanceOf(Struct::class, $subject);
        static::assertEquals('123', $subject->foo);
        static::assertEquals('456', $subject->bar);
    }

    /**
     * @since [*next-version*]
     */
    public function testCreateMissingProp()
    {
        $data = [
            'bar' => '456',
        ];

        $subject = new class($data) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willReturnArg()->defaultsTo('DEFAULT'),
                    'bar' => MockPropType::create()->willReturnArg()->defaultsTo('DEFAULT'),
                ];
            }
        };

        static::assertInstanceOf(Struct::class, $subject);
        static::assertEquals('DEFAULT', $subject->foo);
        static::assertEquals('456', $subject->bar);
    }

    /**
     * @since [*next-version*]
     */
    public function testCreateExtraProp()
    {
        $this->expectException(LogicException::class);

        $data = [
            'invalid' => 'invalid',
            'foo' => '123',
            'bar' => '456',
        ];

        new class($data) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willReturnArg(),
                    'bar' => MockPropType::create()->willReturnArg(),
                ];
            }
        };
    }

    /**
     * @since [*next-version*]
     */
    public function testFromArray()
    {
        $data = [
            'foo' => '123',
            'bar' => '456',
        ];

        $dummy = new class($data) extends Struct {
            public static $ctorCalled = 0;

            public function __construct(array $data = [])
            {
                parent::__construct($data);
                static::$ctorCalled++;
            }

            protected static function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willReturnArg(),
                    'bar' => MockPropType::create()->willReturnArg(),
                ];
            }
        };

        // Reset the counter, since creating the dummy instance increments it
        $dummy::$ctorCalled = 0;

        $subject = $dummy::fromArray($data);

        // Assert that the ctor was called
        static::assertEquals(1, $dummy::$ctorCalled);
        // Assert that the new instance is of the same class as the dummy
        static::assertInstanceOf(get_class($dummy), $subject);
    }

    /**
     * @since [*next-version*]
     */
    public function testGetPropTypesCache()
    {
        $subject = new class(['foo' => '', 'bar' => '', 'baz' => '']) extends Struct {
            public static $numCalled = 0;

            protected static function propTypes() : array
            {
                static::$numCalled++;

                return [
                    'foo' => MockPropType::create()->willReturnArg(),
                    'bar' => MockPropType::create()->willReturnArg(),
                    'baz' => MockPropType::create()->willReturnArg(),
                ];
            }
        };

        // The prop types are fetched when a property is read
        $subject->foo;
        $subject->bar;
        $subject->baz;

        // Assert only called once
        static::assertEquals(1, $subject::$numCalled);
    }

    /**
     * @since [*next-version*]
     */
    public function testReadProp()
    {
        $data = [
            'foo' => '123',
        ];

        $subject = new class($data) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willReturn('456'),
                ];
            }
        };

        self::assertEquals('456', $subject->foo);
    }

    /**
     * @since [*next-version*]
     */
    public function testReadPropDefault()
    {
        $subject = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willReturn('casted')->defaultsTo('default'),
                ];
            }
        };

        self::assertEquals('default', $subject->foo);
    }

    /**
     * @since [*next-version*]
     */
    public function testReadPropUndefined()
    {
        $this->expectException(LogicException::class);

        $subject = new class(['foo' => '123']) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willReturnArg(),
                ];
            }
        };

        $subject->bar;
    }

    /**
     * @since [*next-version*]
     */
    public function testSetProp()
    {
        $this->expectException(LogicException::class);

        $subject = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willReturnArg(),
                ];
            }
        };

        $subject->foo = uniqid('new-value');
    }

    /**
     * @since [*next-version*]
     */
    public function testDerive()
    {
        $data = [
            'foo' => 'foo',
            'bar' => 'bar',
        ];

        $subject = new class($data) extends Struct {
            static protected $__propTypesCache;

            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willDo(function ($arg) {
                        return $arg . '!';
                    }),
                    'bar' => MockPropType::create()->willDo(function ($arg) {
                        return $arg . '!';
                    }),
                ];
            }
        };

        $newStruct = $subject::derive($subject, ['foo' => 'NEW']);

        // Check if new instance is of the same class
        static::assertInstanceOf(get_class($subject), $newStruct);
        // Check that the original and new instances are not the same
        static::assertNotSame($newStruct, $subject);

        // Check that new struct has incorporated the changes, including any casting modifications, where appropriate
        static::assertEquals('NEW!', $newStruct->foo);
        static::assertEquals('bar!', $newStruct->bar);

        // Check original struct left unchanged
        static::assertEquals('foo!', $subject->foo);
        static::assertEquals('bar!', $subject->bar);
    }

    /**
     * @since [*next-version*]
     */
    public function testDeriveNoChanges()
    {
        $data = [
            'foo' => 'foo',
            'bar' => 'bar',
        ];

        $subject = new class($data) extends Struct {
            static protected $__propTypesCache;

            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willDo(function ($arg) {
                        return $arg . '!';
                    }),
                    'bar' => MockPropType::create()->willDo(function ($arg) {
                        return $arg . '!';
                    }),
                ];
            }
        };

        $newStruct = $subject::derive($subject, []);

        // Check that the new instance is the same as the original
        static::assertSame($newStruct, $subject);

        // Check whether the struct is left unchanged
        static::assertEquals('foo!', $subject->foo);
        static::assertEquals('bar!', $subject->bar);
    }

    /**
     * @since [*next-version*]
     */
    public function testDeriveInvalidProp()
    {
        $this->expectException(LogicException::class);

        $subject = new class([]) extends Struct {
            static protected $__propTypesCache;

            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        Struct::derive($subject, [
            'invalid' => 'invalid',
        ]);
    }

    /**
     * @since [*next-version*]
     */
    public function testEquals()
    {
        $data = [
            'a' => 'a',
            'b' => 'b',
        ];

        $struct1 = new class($data) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'a' => MockPropType::create(),
                    'b' => MockPropType::create(),
                ];
            }
        };

        $struct2 = new class($data) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'a' => MockPropType::create(),
                    'b' => MockPropType::create(),
                ];
            }
        };

        static::assertTrue(Struct::equals($struct1, $struct2));
        static::assertTrue(Struct::equals($struct2, $struct1));
    }

    /**
     * @since [*next-version*]
     */
    public function testEqualsDiffData()
    {
        $data1 = [
            'a' => '1',
            'b' => '2',
        ];

        $data2 = [
            'a' => '1',
            'b' => '3',
        ];

        $struct1 = new class($data1) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'a' => MockPropType::create(),
                    'b' => MockPropType::create(),
                ];
            }
        };

        $struct2 = new class($data2) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'a' => MockPropType::create(),
                    'b' => MockPropType::create(),
                ];
            }
        };

        static::assertFalse(Struct::equals($struct1, $struct2));
        static::assertFalse(Struct::equals($struct2, $struct1));
    }

    /**
     * @since [*next-version*]
     */
    public function testEqualsDiffPropsSameData()
    {
        $data = [
            'a' => 'a',
            'b' => 'b',
        ];

        $struct1 = new class($data) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'a' => MockPropType::create()->willReturn('A'),
                    'b' => MockPropType::create()->willReturn('B'),
                ];
            }
        };

        $struct2 = new class($data) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'a' => MockPropType::create()->willReturn('A'),
                    'b' => MockPropType::create()->willReturn('2'),
                ];
            }
        };

        static::assertFalse(Struct::equals($struct1, $struct2));
        static::assertFalse(Struct::equals($struct2, $struct1));
    }

    /**
     * @since [*next-version*]
     */
    public function testToArray()
    {
        $data = [
            'foo' => '123',
            'bar' => '456',
        ];

        $expected = [
            'foo' => 'abc',
            'bar' => 'def',
        ];

        $subject = new class($data) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->expects('123')->willReturn('abc'),
                    'bar' => MockPropType::create()->expects('456')->willReturn('def'),
                ];
            }
        };

        self::assertEquals($expected, Struct::toArray($subject));
        self::assertEquals($expected, $subject->__debugInfo());
    }

    /**
     * @since [*next-version*]
     */
    public function testSerialize()
    {
        $data = [
            'foo' => '123',
            'bar' => '456',
        ];

        $subject = new class($data) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->expects('123')->willReturn('abc'),
                    'bar' => MockPropType::create()->expects('456')->willReturn('def'),
                ];
            }
        };

        $expected = serialize([
            'foo' => 'abc',
            'bar' => 'def',
        ]);

        self::assertEquals($expected, $subject->serialize());
    }

    /**
     * @since [*next-version*]
     */
    public function testUnserialize()
    {
        $subject = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        $serialized = serialize([
            'foo' => '123',
            'bar' => '456',
        ]);

        $subject->unserialize($serialized);

        self::assertEquals('123', $subject->foo);
        self::assertEquals('456', $subject->bar);
    }
}
