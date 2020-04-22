<?php

namespace Dhii\Structs\Tests\Unit\PropsTypes;

use Dhii\Structs\PropType;
use Dhii\Structs\PropTypes\StructPropType;
use Dhii\Structs\Struct;
use Dhii\Structs\Tests\Stubs\MockPropType;
use LogicException;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @since [*next-version*]
 */
class StructPropTypeUnitTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testCreate()
    {
        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct));

        static::assertInstanceOf(PropType::class, $subject);
    }

    /**
     * @since [*next-version*]
     */
    public function testCreateClassNotExists()
    {
        $this->expectException(LogicException::class);

        new StructPropType('SeriouslyHopeThatThisClassDoesNotExistOtherwiseThisTestWillWronglyPass');
    }

    /**
     * @since [*next-version*]
     */
    public function testCreateClassNotExtendsStruct()
    {
        $this->expectException(LogicException::class);

        $obj = new class {
        };

        new StructPropType(get_class($obj));
    }

    /**
     * @since [*next-version*]
     */
    public function testGetName()
    {
        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        $expected = get_class($struct);
        $subject = new StructPropType($expected);

        static::assertEquals($expected, $subject->getName());
    }

    /**
     * @since [*next-version*]
     */
    public function testGetDefault()
    {
        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct));

        static::assertNull($subject->getDefault());
    }

    /**
     * @since [*next-version*]
     */
    public function testIsValidStructSameClass()
    {
        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct));

        static::assertTrue($subject->isValid($struct));
    }

    /**
     * @since [*next-version*]
     */
    public function testIsValidStructDiffClassAndProps()
    {
        $struct1 = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        $struct2 = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'baz' => MockPropType::create(),
                    'bad' => MockPropType::create(),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct1));

        static::assertFalse($subject->isValid($struct2));
    }

    /**
     * @since [*next-version*]
     */
    public function testIsValidStructDiffClassSameProps()
    {
        $struct1 = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        $struct2 = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct1));

        static::assertFalse($subject->isValid($struct2));
    }

    /**
     * @since [*next-version*]
     */
    public function testIsValidArray()
    {
        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->validatesTo(true),
                    'bar' => MockPropType::create()->validatesTo(true),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct));

        $input = [
            'foo' => 'foo',
            'bar' => 'bar',
        ];

        static::assertTrue($subject->isValid($input));
    }

    /**
     * @since [*next-version*]
     */
    public function testIsValidArrayInvalidProp()
    {
        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->validatesTo(true),
                    'bar' => MockPropType::create()->validatesTo(false),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct));

        $input = [
            'foo' => 'foo',
            'bar' => 'bar',
        ];

        static::assertFalse($subject->isValid($input));
    }

    /**
     * @since [*next-version*]
     */
    public function testIsValidArrayMissingProp()
    {
        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->validatesTo(true),
                    'bar' => MockPropType::create()->validatesTo(true),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct));

        $input = [
            'foo' => 'foo',
        ];

        static::assertTrue($subject->isValid($input));
    }

    /**
     * @since [*next-version*]
     */
    public function testIsValidArrayExtraProp()
    {
        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->validatesTo(true),
                    'bar' => MockPropType::create()->validatesTo(true),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct));

        $input = [
            'foo' => 'foo',
            'bar' => 'bar',
            'extra' => 'extra',
        ];

        static::assertFalse($subject->isValid($input));
    }

    /**
     * @since [*next-version*]
     */
    public function testCastStructSameClass()
    {
        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct));

        static::assertSame($struct, $subject->cast($struct));
    }

    /**
     * @since [*next-version*]
     */
    public function testCastStructDiffClassAndProps()
    {
        $this->expectException(TypeError::class);

        $struct1 = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        $struct2 = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'baz' => MockPropType::create(),
                    'bad' => MockPropType::create(),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct1));

        $subject->cast($struct2);
    }

    /**
     * @since [*next-version*]
     */
    public function testCastStructDiffClassSameProps()
    {
        $this->expectException(TypeError::class);

        $struct1 = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        $struct2 = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create(),
                    'bar' => MockPropType::create(),
                ];
            }
        };

        $subject = new StructPropType(get_class($struct1));

        $subject->cast($struct2);
    }

    /**
     * @since [*next-version*]
     */
    public function testCastArray()
    {
        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willReturn('casted-foo'),
                    'bar' => MockPropType::create()->willReturn('casted-bar'),
                ];
            }
        };

        $class = get_class($struct);
        $subject = new StructPropType($class);

        $input = [
            'foo' => 'foo',
            'bar' => 'bar',
        ];

        $result = $subject->cast($input);

        static::assertInstanceOf($class, $result);
        static::assertEquals('casted-foo', $result->foo);
        static::assertEquals('casted-bar', $result->bar);
    }

    /**
     * @since [*next-version*]
     */
    public function testCastArrayMissingProp()
    {
        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willReturn('casted-foo'),
                    'bar' => MockPropType::create()->willReturn('casted-bar')->defaultsTo('default-bar'),
                ];
            }
        };

        $class = get_class($struct);
        $subject = new StructPropType($class);

        $input = [
            'foo' => 'foo',
        ];

        $result = $subject->cast($input);

        static::assertInstanceOf($class, $result);
        static::assertEquals('casted-foo', $result->foo);
        static::assertEquals('default-bar', $result->bar);
    }

    /**
     * @since [*next-version*]
     */
    public function testCastArrayInvalidProp()
    {
        $this->expectException(TypeError::class);

        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willReturn('casted-foo'),
                    'bar' => MockPropType::create()->willThrow(),
                ];
            }
        };

        $class = get_class($struct);
        $subject = new StructPropType($class);

        $input = [
            'foo' => 'foo',
            'bar' => 'bar',
        ];

        $subject->cast($input);
    }

    /**
     * @since [*next-version*]
     */
    public function testCastArrayExtraProp()
    {
        $this->expectException(LogicException::class);

        $struct = new class([]) extends Struct {
            static protected function propTypes() : array
            {
                return [
                    'foo' => MockPropType::create()->willReturn('casted-foo'),
                    'bar' => MockPropType::create()->willReturn('casted-bar'),
                ];
            }
        };

        $class = get_class($struct);
        $subject = new StructPropType($class);

        $input = [
            'foo' => 'foo',
            'bar' => 'bar',
            'extra' => 'extra',
        ];

        $subject->cast($input);
    }
}
