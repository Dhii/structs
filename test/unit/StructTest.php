<?php

namespace Dhii\Structs\Tests\Unit;

use BadMethodCallException;
use Dhii\Structs\PropType;
use Dhii\Structs\PropTypes\NullablePropType;
use Dhii\Structs\PropTypes\StringPropType;
use Dhii\Structs\Struct;
use Dhii\Structs\Tests\Stubs\StructStub;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * @since [*next-version*]
 */
class StructTest extends TestCase
{
    /**
     * Creates an instance of the {@link Struct} class, with a given list of property types and set of data.
     *
     * @since [*next-version*]
     *
     * @param array $propTypes The property types.
     * @param array $data      The struct data.
     *
     * @return MockObject&Struct
     */
    protected function createSubject(array $propTypes, array $data = [])
    {
        /* @var $mock MockObject&Struct */
        $mock = $this->getMockBuilder(Struct::class)
                     ->disableOriginalConstructor()
                     ->setMethods(['getPropTypes'])
                     ->getMockForAbstractClass();

        $mock->method('getPropTypes')->willReturn($propTypes);

        $mock->__construct($data);

        return $mock;
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testCreate()
    {
        {
            $fooType = $this->getMockForAbstractClass(PropType::class);
            $barType = $this->getMockForAbstractClass(PropType::class);

            $fooType->method('cast')->willReturnArgument(0);
            $barType->method('cast')->willReturnArgument(0);

            $propTypes = [
                'foo' => $fooType,
                'bar' => $barType,
            ];
        }
        {
            $foo = uniqid('foo');
            $bar = uniqid('bar');
        }

        $subject = $this->createSubject($propTypes, [
            'foo' => $foo,
            'bar' => $bar,
        ]);

        static::assertInstanceOf(Struct::class, $subject);
        static::assertEquals($foo, $subject->foo);
        static::assertEquals($bar, $subject->bar);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testCreateInvalidProp()
    {
        $this->expectException(LogicException::class);

        {
            $fooType = $this->getMockForAbstractClass(PropType::class);
            $barType = $this->getMockForAbstractClass(PropType::class);

            $fooType->method('cast')->willReturnArgument(0);
            $barType->method('cast')->willReturnArgument(0);

            $propTypes = [
                'foo' => $fooType,
                'bar' => $barType,
            ];
        }

        $this->createSubject($propTypes, [
            'invalid' => 'invalid',
            'bar' => uniqid('bar'),
        ]);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testReadProp()
    {
        {
            $value = uniqid('value');
            $casted = uniqid('casted');
        }
        {
            $propType = $this->getMockForAbstractClass(PropType::class);
            $propType->method('cast')->willReturn($casted);

            $propTypes = [
                'foo' => $propType,
            ];
        }
        {
            $data = [
                'foo' => $value,
            ];
        }

        $subject = $this->createSubject($propTypes, $data);

        self::assertEquals($casted, $subject->foo);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testReadPropDefault()
    {
        {
            $default = uniqid('default');
        }
        {
            $propType = $this->getMockForAbstractClass(PropType::class);
            $propType->method('getDefault')->willReturn($default);

            $propTypes = [
                'foo' => $propType,
            ];
        }

        $subject = $this->createSubject($propTypes, []);

        self::assertEquals($default, $subject->foo);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testReadPropUndefined()
    {
        $this->expectException(LogicException::class);

        {
            $propType = $this->getMockForAbstractClass(PropType::class);

            $propTypes = [
                'foo' => $propType,
            ];
        }

        $subject = $this->createSubject($propTypes, []);

        $subject->bar;
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testSetProp()
    {
        $this->expectException(LogicException::class);

        {
            $propType = $this->getMockForAbstractClass(PropType::class);

            $propTypes = [
                'foo' => $propType,
            ];
        }

        $subject = $this->createSubject($propTypes, []);

        $subject->foo = uniqid('new-value');
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testDerive()
    {
        {
            // Original values given to constructor
            $ogFoo = uniqid('foo');
            $ogBar = uniqid('bar');

            // Casted versions of values given to constructor
            $castedFoo = uniqid('casted-foo');
            $castedBar = uniqid('casted-bar');

            // New value given to with()
            $newFoo = uniqid('new-foo');

            // Casted version of value given to with()
            $castedNewFoo = uniqid('casted-new-foo');
        }
        {
            $fooType = $this->getMockForAbstractClass(PropType::class);
            $barType = $this->getMockForAbstractClass(PropType::class);

            // Cast method are called twice: once during constructor, once during with()
            $fooType->expects(static::exactly(2))
                    ->method('cast')
                    ->withConsecutive([$ogFoo], [$newFoo])
                    ->willReturnOnConsecutiveCalls($castedFoo, $castedNewFoo);

            $barType->expects(static::once())
                    ->method('cast')
                    ->with($ogBar)
                    ->willReturn($castedBar);

            $propTypes = [
                'foo' => $fooType,
                'bar' => $barType,
            ];
        }

        $subject = $this->createSubject($propTypes, [
            'foo' => $ogFoo,
            'bar' => $ogBar,
        ]);

        $newStruct = Struct::derive($subject, ['foo' => $newFoo]);

        static::assertInstanceOf(Struct::class, $newStruct);
        static::assertNotSame($newStruct, $subject);

        // Check that new struct has correct changes
        static::assertEquals($castedNewFoo, $newStruct->foo);
        static::assertEquals($castedBar, $newStruct->bar);

        // Check original struct left unchanged
        static::assertEquals($castedFoo, $subject->foo);
        static::assertEquals($castedBar, $subject->bar);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testDeriveNoChanges()
    {
        {
            // Original values given to constructor
            $ogFoo = uniqid('foo');
            $ogBar = uniqid('bar');

            // Casted versions of values given to constructor
            $castedFoo = uniqid('casted-foo');
            $castedBar = uniqid('casted-bar');;
        }
        {
            $fooType = $this->getMockForAbstractClass(PropType::class);
            $barType = $this->getMockForAbstractClass(PropType::class);

            $fooType->expects(static::once())
                    ->method('cast')
                    ->with($ogFoo)
                    ->willReturn($castedFoo);

            $barType->expects(static::once())
                    ->method('cast')
                    ->with($ogBar)
                    ->willReturn($castedBar);

            $propTypes = [
                'foo' => $fooType,
                'bar' => $barType,
            ];
        }

        $subject = $this->createSubject($propTypes, [
            'foo' => $ogFoo,
            'bar' => $ogBar,
        ]);

        $newStruct = Struct::derive($subject, []);

        static::assertInstanceOf(Struct::class, $newStruct);
        static::assertSame($newStruct, $subject);

        // Check struct left unchanged
        static::assertEquals($castedFoo, $subject->foo);
        static::assertEquals($castedBar, $subject->bar);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testDeriveInvalidProp()
    {
        $this->expectException(LogicException::class);

        {
            $fooType = $this->getMockForAbstractClass(PropType::class);
            $barType = $this->getMockForAbstractClass(PropType::class);

            $propTypes = [
                'foo' => $fooType,
                'bar' => $barType,
            ];
        }

        $subject = $this->createSubject($propTypes, []);

        Struct::derive($subject, [
            'invalid' => 'invalid',
        ]);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testAreEqual()
    {
        {
            $aType = $this->getMockForAbstractClass(PropType::class);
            $bType = $this->getMockForAbstractClass(PropType::class);

            $aType->method('cast')->willReturnArgument(0);
            $bType->method('cast')->willReturnArgument(0);

            $propTypes = [
                'a' => $aType,
                'b' => $bType,
            ];
        }
        {
            $data = [
                'a' => uniqid('a'),
                'b' => uniqid('b'),
            ];

            $struct1 = $this->createSubject($propTypes, $data);
            $struct2 = $this->createSubject($propTypes, $data);
        }

        static::assertTrue(Struct::areEqual($struct1, $struct2));
        static::assertTrue(Struct::areEqual($struct2, $struct1));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testAreEqualThreeStructs()
    {
        {
            $aType = $this->getMockForAbstractClass(PropType::class);
            $bType = $this->getMockForAbstractClass(PropType::class);

            $aType->method('cast')->willReturnArgument(0);
            $bType->method('cast')->willReturnArgument(0);

            $propTypes = [
                'a' => $aType,
                'b' => $bType,
            ];
        }
        {
            $data = [
                'a' => uniqid('a'),
                'b' => uniqid('b'),
            ];

            $struct1 = $this->createSubject($propTypes, $data);
            $struct2 = $this->createSubject($propTypes, $data);
            $struct3 = $this->createSubject($propTypes, $data);
        }

        static::assertTrue(Struct::areEqual($struct1, $struct2, $struct3));
        static::assertTrue(Struct::areEqual($struct3, $struct2, $struct1));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testAreEqualDiffData()
    {
        {
            $aType = $this->getMockForAbstractClass(PropType::class);
            $bType = $this->getMockForAbstractClass(PropType::class);

            $aType->method('cast')->willReturnArgument(0);
            $bType->method('cast')->willReturnArgument(0);

            $propTypes = [
                'a' => $aType,
                'b' => $bType,
            ];
        }
        {
            $data1 = [
                'a' => uniqid('a'),
                'b' => uniqid('b'),
            ];
            $data2 = [
                'a' => uniqid('a'),
                'b' => uniqid('b'),
            ];
            $struct1 = $this->createSubject($propTypes, $data1);
            $struct2 = $this->createSubject($propTypes, $data1);
            $struct3 = $this->createSubject($propTypes, $data2);
        }

        static::assertFalse(Struct::areEqual($struct1, $struct2, $struct3));
        static::assertFalse(Struct::areEqual($struct3, $struct2, $struct1));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testAreEqualDiffPropsSameData()
    {
        {
            // Cannot use mocks in this test due to a recursive dependency in the mock class, which causes PHP's
            // loose equivalence to recurse indefinitely
            $aType = new StringPropType();
            $bType1 = new StringPropType();
            $bType2 = new NullablePropType(new StringPropType());

            $propTypes1 = [
                'a' => $aType,
                'b' => $bType1,
            ];

            $propTypes2 = [
                'a' => $aType,
                'b' => $bType2,
            ];
        }
        {
            $data = [
                'a' => uniqid('a'),
                'b' => uniqid('b'),
            ];

            $struct1 = $this->createSubject($propTypes1, $data);
            $struct2 = $this->createSubject($propTypes2, $data);
        }

        static::assertFalse(Struct::areEqual($struct1, $struct2));
        static::assertFalse(Struct::areEqual($struct2, $struct1));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testAreEqualOnlyOneArg()
    {
        $this->expectException(BadMethodCallException::class);

        {
            $aType = $this->getMockForAbstractClass(PropType::class);
            $bType = $this->getMockForAbstractClass(PropType::class);

            $aType->method('cast')->willReturnArgument(0);
            $bType->method('cast')->willReturnArgument(0);

            $propTypes = [
                'a' => $aType,
                'b' => $bType,
            ];
        }
        {
            $data = [
                'a' => uniqid('a'),
                'b' => uniqid('b'),
            ];

            $struct = $this->createSubject($propTypes, $data);
        }

        Struct::areEqual($struct);
    }

    /**
     * @since [*next-version*]
     */
    public function testAreEqualNoArgs()
    {
        $this->expectException(BadMethodCallException::class);

        Struct::areEqual();
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testToArray()
    {
        {
            $foo = uniqid('foo');
            $bar = uniqid('bar');

            $castedFoo = uniqid('casted-foo');
            $castedBar = uniqid('casted-bar');
        }
        {
            $fooPropTy = $this->getMockForAbstractClass(PropType::class);
            $barPropTy = $this->getMockForAbstractClass(PropType::class);

            $fooPropTy->method('cast')->willReturn($castedFoo);
            $barPropTy->method('cast')->willReturn($castedBar);

            $propTypes = [
                'foo' => $fooPropTy,
                'bar' => $barPropTy,
            ];
        }
        {
            $data = [
                'foo' => $foo,
                'bar' => $bar,
            ];
        }

        $subject = $this->createSubject($propTypes, $data);

        $expected = [
            'foo' => $castedFoo,
            'bar' => $castedBar,
        ];

        self::assertEquals($expected, Struct::toArray($subject));
        self::assertEquals($expected, $subject->__debugInfo());
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testSerialize()
    {
        {
            $foo = uniqid('foo');
            $bar = uniqid('bar');

            $castedFoo = uniqid('casted-foo');
            $castedBar = uniqid('casted-bar');
        }
        {
            $fooPropTy = $this->getMockForAbstractClass(PropType::class);
            $barPropTy = $this->getMockForAbstractClass(PropType::class);

            $fooPropTy->method('cast')->willReturn($castedFoo);
            $barPropTy->method('cast')->willReturn($castedBar);

            $propTypes = [
                'foo' => $fooPropTy,
                'bar' => $barPropTy,
            ];
        }
        {
            $data = [
                'foo' => $foo,
                'bar' => $bar,
            ];
        }

        $subject = $this->createSubject($propTypes, $data);

        $expected = serialize([
            'foo' => $castedFoo,
            'bar' => $castedBar,
        ]);

        self::assertEquals($expected, $subject->serialize());
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testUnserialize()
    {
        {
            $foo = uniqid('foo');
            $bar = uniqid('bar');
        }
        {
            $fooPropTy = $this->getMockForAbstractClass(PropType::class);
            $barPropTy = $this->getMockForAbstractClass(PropType::class);

            $propTypes = [
                'foo' => $fooPropTy,
                'bar' => $barPropTy,
            ];
        }
        {
            $serialized = serialize([
                'foo' => $foo,
                'bar' => $bar,
            ]);
        }

        $subject = $this->createSubject($propTypes, []);
        $subject->unserialize($serialized);

        self::assertEquals($foo, $subject->foo);
        self::assertEquals($bar, $subject->bar);
    }
}
