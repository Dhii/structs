<?php

namespace Dhii\Structs\Tests\Stubs;

use Dhii\Structs\Struct;

/**
 * Struct stub used for testing inheritance.
 *
 * @since [*next-version*]
 */
class StructStub extends Struct
{
    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    static protected function propTypes() : array
    {
        return [
            'foo' => MockPropType::create()->willReturnArg(),
            'bar' => MockPropType::create()->willReturnArg(),
        ];
    }
}
