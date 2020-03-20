# Dhii - Functions

[![Build Status](https://travis-ci.org/dhii/structs.svg?branch=master)](https://travis-ci.org/dhii/structs)
[![Code Climate](https://codeclimate.com/github/dhii/structs/badges/gpa.svg)](https://codeclimate.com/github/dhii/structs)
[![Test Coverage](https://codeclimate.com/github/dhii/structs/badges/coverage.svg)](https://codeclimate.com/github/dhii/structs/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/structs/version)](https://packagist.org/packages/dhii/structs)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

Immutable, data-only classes

## Introduction

Often times our code needs to move sets of data around between various units. These can be data models, database
records, configuration, etc. Due to missing features in PHP (at the time of writing), the only way to have these data
sets be immutable **and** type-safe is to declare a class with `protected` properties and define corresponding getter
methods for each property.

It's also a common occurrence that these data sets are required to be hydrated from a simpler data type, such as a
native PHP `array`, or de-hydrated into one. Implementing this logic for each data set class is a cumbersome task.

This package introduces a user-land version of structs; data-only classes that are type-safe and immutable, with the
added benefit of being easily serialized and un-serialized.

## Usage

Extend the `Struct` abstract class and declare the property types by implementing `getPropTypes()`:

```php
use Dhii\Structs\Struct;
use Dhii\Structs\Ty;

/**
 * For IDE IntelliSense auto-completion,
 * declare the properties in the class docblock:
 *
 * @property-read string $url
 * @property-read int $width
 * @property-read int $height
 */
class Image extends Struct
{
    public function getPropTypes() : array
    {
        return [
            'url' => Ty::string(),
            'width' => Ty::int(),
            'height' => Ty::int(),
        ];
    }
}
```

You can then access the struct's properties using normal PHP object property read syntax:

```php
$image = new Image([
    'url' => "http://example.com/image.png",
    'width' => 800,
    'height' => 600,
]);

$image->url; // "http://example.com/image.png"
$image->width; // 800
$image->height; // 600
```

Custom constructors _may_ be defined. However be wary that this will prevent you from creating instances from arrays.

```php
class Image extends Struct
{
    // Overriding constructors should call the parent constructor
    public function __construct(string $url, int $width, int $height) {
        parent::__construct([
            'url' => $url,
            'width' => $width,
            'height' => $height
        ]);
    }

    public function getPropTypes() : array { /* ... */ }
}
```

Consider adding a static "constructor" method instead.

```php
class Image extends Struct
{
    public function getPropTypes() : array { /* ... */ }

    public static function create(string $url, int $width, int $height) {
        return new static([
            'url' => $url,
            'width' => $width,
            'height' => $height
        ]);
    }
}
```

Structs can be copied using the `with()` method, which leaves the original struct unchanged. Multiple properties changes
may be specified in bulk:

```php
$image2 = $image->with(['url' => 'https://example.com/image2.png']);

$flipped = $image->with([
    'width' => $image->height,
    'height' => $image->width
]);
```

## Property Types

| Class | Shorthand | Value types |
|-------|--------|------|
| `MixedPropType` | `Ty::mixed()` | Any value |
| `BoolPropType` | `Ty::bool()` | Booleans and [boolean-castable][1] values |
| `IntPropType` | `Ty::int()` | Integers and [integer-castable][2] values |
| `FloatPropType` | `Ty::float()` | Floats and [float-castable][3] values |
| `StringPropType` | `Ty::string()` | Strings, [string-castable][4] values and objects that implement [`__toString()`][5] |
| `ArrayPropType` | `Ty::array()` | Array values |
| `ObjectPropType` | `Ty::object(?)` | Object values, with an optional `instanceof` restriction |
| `CallablePropType` | `Ty::callable()` | [Callable values][6] |
| `EnumPropType` | `Ty::enum([...])` | Values that exist within a pre-defined set |
| `UnionPropType` | `Ty::union([...])` | Values that match **at least one** type in a given set |
| `IntersectionPropType` | `Ty::intersect([...])` | Values that match **all** of the types in a given set |
| `NullablePropType` | `Ty::nullable(?)` | Values that match a given type, or `null` |

Examples:

```php
use Dhii\Structs\Struct;
use Dhii\Structs\Ty;

class MyStruct extends Struct
{
    public function getPropTypes() : array {
        return [
            'owner' => Ty::nullable(Ty::object(UserInterface::class)),
            'day' => Ty::enum('sun', 'mon', 'tues', 'wed', 'thurs', 'fri', 'sat'),
            'config' => Ty::union([Ty::array(), Ty::object()]),
            'list' => Ty::intersect([Ty::object(Traversable::class), Ty::object(Countable::class)]),
        ];
    }
}
```

[1]: https://www.php.net/manual/en/language.types.boolean.php#language.types.boolean.casting
[2]: https://www.php.net/manual/en/language.types.integer.php#language.types.integer.casting
[3]: https://www.php.net/manual/en/language.types.float.php#language.types.float.casting
[4]: https://www.php.net/manual/en/language.types.string.php#language.types.string.casting
[5]: https://www.php.net/manual/en/language.oop5.magic.php#object.tostring
[6]: https://www.php.net/manual/en/function.is-callable.php

[Dhii]: https://github.com/Dhii/dhii
