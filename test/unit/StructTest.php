<?php

namespace Dhii\Structs\Tests\Unit;

use Dhii\Structs\PropType;
use Dhii\Structs\Struct;
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
     */
    public function testCreate()
    {
        $subject = $this->createSubject([], []);

        static::assertInstanceOf(Struct::class, $subject);
    }

    /**
     * @since [*next-version*]
     */
    public function testCreateInvalidProp()
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

        $this->createSubject($propTypes, [
            'invalid' => 'invalid',
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
    public function testWith()
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

        $newStruct = $subject->with(['foo' => $newFoo]);

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
    public function testWithNoChanges()
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

        $newStruct = $subject->with([]);

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
    public function testWithInvalidProp()
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

        $subject->with([
            'invalid' => 'invalid',
        ]);
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testIsEqualTo()
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

        static::assertTrue($struct1->isEqualTo($struct2));
        static::assertTrue($struct2->isEqualTo($struct1));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testIsEqualToDiffData()
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
            $struct1 = $this->createSubject($propTypes, [
                'a' => uniqid('a'),
                'b' => uniqid('b'),
            ]);
            $struct2 = $this->createSubject($propTypes, [
                'a' => uniqid('a'),
                'b' => uniqid('b'),
            ]);
        }

        static::assertFalse($struct1->isEqualTo($struct2));
        static::assertFalse($struct2->isEqualTo($struct1));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testIsEqualToDiffPropsSameData()
    {
        {
            $aType = $this->getMockForAbstractClass(PropType::class);
            $bType1 = $this->getMockForAbstractClass(PropType::class);
            $bType2 = $this->getMockForAbstractClass(PropType::class);

            $aType->method('cast')->willReturnArgument(0);
            $bType1->method('cast')->willReturnArgument(0);
            $bType2->method('cast')->willReturnArgument(0);

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
            $struct1 = $this->createSubject($propTypes1, [
                'a' => uniqid('a'),
                'b' => uniqid('b'),
            ]);
            $struct2 = $this->createSubject($propTypes2, [
                'a' => uniqid('a'),
                'b' => uniqid('b'),
            ]);
        }

        static::assertFalse($struct1->isEqualTo($struct2));
        static::assertFalse($struct2->isEqualTo($struct1));
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

        self::assertEquals($expected, $subject->toArray());
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
