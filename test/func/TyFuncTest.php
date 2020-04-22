<?php

namespace Dhii\Structs\Tests\Func;

use ArrayAccess;
use ArrayObject;
use Countable;
use Dhii\Structs\PropType;
use Dhii\Structs\PropTypes\ArrayPropType;
use Dhii\Structs\PropTypes\BoolPropType;
use Dhii\Structs\PropTypes\CallablePropType;
use Dhii\Structs\PropTypes\CustomPropType;
use Dhii\Structs\PropTypes\EnumPropType;
use Dhii\Structs\PropTypes\FloatPropType;
use Dhii\Structs\PropTypes\IntersectionPropType;
use Dhii\Structs\PropTypes\IntPropType;
use Dhii\Structs\PropTypes\IterablePropType;
use Dhii\Structs\PropTypes\MixedPropType;
use Dhii\Structs\PropTypes\NullablePropType;
use Dhii\Structs\PropTypes\ObjectPropType;
use Dhii\Structs\PropTypes\StringPropType;
use Dhii\Structs\PropTypes\StructPropType;
use Dhii\Structs\PropTypes\UnionPropType;
use Dhii\Structs\Struct;
use Dhii\Structs\Ty;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Traversable;

/**
 * @since [*next-version*]
 */
class TyFuncTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testMixed()
    {
        static::assertInstanceOf(MixedPropType::class, Ty::mixed());
    }

    /**
     * @since [*next-version*]
     */
    public function testInt()
    {
        static::assertInstanceOf(IntPropType::class, Ty::int());
    }

    /**
     * @since [*next-version*]
     */
    public function testFloat()
    {
        static::assertInstanceOf(FloatPropType::class, Ty::float());
    }

    /**
     * @since [*next-version*]
     */
    public function testBool()
    {
        static::assertInstanceOf(BoolPropType::class, Ty::bool());
    }

    /**
     * @since [*next-version*]
     */
    public function testString()
    {
        static::assertInstanceOf(StringPropType::class, Ty::string());
    }

    /**
     * @since [*next-version*]
     */
    public function testCallable()
    {
        static::assertInstanceOf(CallablePropType::class, Ty::callable());
    }

    /**
     * @since [*next-version*]
     */
    public function testArray()
    {
        static::assertInstanceOf(ArrayPropType::class, Ty::array());
    }

    /**
     * @since [*next-version*]
     */
    public function testArrayOf()
    {
        static::assertInstanceOf(ArrayPropType::class, Ty::arrayOf(Ty::object(Countable::class)));
    }

    /**
     * @since [*next-version*]
     */
    public function testArrayLike()
    {
        $subject = Ty::arrayLike();

        static::assertInstanceOf(UnionPropType::class, $subject);
        static::assertTrue($subject->isValid([]));
        static::assertTrue($subject->isValid(['some', 'array']));
        static::assertTrue($subject->isValid(new ArrayObject([])));
        static::assertTrue($subject->isValid(new ArrayObject(['some', 'array'])));
    }

    /**
     * @since [*next-version*]
     */
    public function testIterable()
    {
        static::assertInstanceOf(IterablePropType::class, Ty::iterable());
    }

    /**
     * @since [*next-version*]
     */
    public function testObject()
    {
        static::assertInstanceOf(ObjectPropType::class, Ty::object());
    }

    /**
     * @since [*next-version*]
     */
    public function testObjectWithParent()
    {
        static::assertInstanceOf(ObjectPropType::class, Ty::object(ArrayAccess::class));
    }

    /**
     * @since [*next-version*]
     */
    public function testObjectMultipleParents()
    {
        $subject = Ty::object(ArrayAccess::class, Countable::class, Traversable::class);

        static::assertInstanceOf(IntersectionPropType::class, $subject);
    }

    /**
     * @since [*next-version*]
     */
    public function testStruct()
    {
        $class = get_class(new class extends Struct {
            static protected function propTypes() : array
            {
                return [];
            }
        });

        static::assertInstanceOf(StructPropType::class, Ty::struct($class));
    }

    /**
     * @since [*next-version*]
     */
    public function testStructCache()
    {
        $class = get_class(new class extends Struct {
            static protected function propTypes() : array
            {
                return [];
            }
        });

        $propTy1 = Ty::struct($class);
        $propTy2 = Ty::struct($class);

        static::assertSame($propTy1, $propTy2);
    }

    /**
     * @since [*next-version*]
     */
    public function testStructCacheDiffClass()
    {
        $class1 = get_class(new class extends Struct {
            static protected function propTypes() : array
            {
                return [];
            }
        });

        $class2 = get_class(new class extends Struct {
            static protected function propTypes() : array
            {
                return [];
            }
        });

        $propTy1 = Ty::struct($class1);
        $propTy2 = Ty::struct($class2);

        static::assertNotSame($propTy1, $propTy2);
    }

    /**
     * @since [*next-version*]
     */
    public function testEnum()
    {
        static::assertInstanceOf(EnumPropType::class, Ty::enum('a', 'b', 'c'));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testUnion()
    {
        /* @var $inner1 MockObject&PropType */
        /* @var $inner2 MockObject&PropType */
        /* @var $inner3 MockObject&PropType */
        $inner1 = $this->getMockForAbstractClass(PropType::class);
        $inner2 = $this->getMockForAbstractClass(PropType::class);
        $inner3 = $this->getMockForAbstractClass(PropType::class);

        static::assertInstanceOf(UnionPropType::class, Ty::union($inner1, $inner2, $inner3));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testIntersection()
    {
        /* @var $inner1 MockObject&PropType */
        /* @var $inner2 MockObject&PropType */
        /* @var $inner3 MockObject&PropType */
        $inner1 = $this->getMockForAbstractClass(PropType::class);
        $inner2 = $this->getMockForAbstractClass(PropType::class);
        $inner3 = $this->getMockForAbstractClass(PropType::class);

        static::assertInstanceOf(IntersectionPropType::class, Ty::intersect($inner1, $inner2, $inner3));
    }

    /**
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testNullable()
    {
        /* @var $inner MockObject&PropType */
        $inner = $this->getMockForAbstractClass(PropType::class);

        static::assertInstanceOf(NullablePropType::class, Ty::nullable($inner));
    }

    /**
     * @since [*next-version*]
     */
    public function testCustom()
    {
        $fn = function () {
        };
        $default = uniqid('default');
        $subject = Ty::custom($fn, $default);

        static::assertInstanceOf(CustomPropType::class, $subject);
        static::assertEquals('<custom>', $subject->getName());
        static::assertEquals($default, $subject->getDefault());
    }
}
