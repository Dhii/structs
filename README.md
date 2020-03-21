# Dhii - Functions

[![Build Status](https://travis-ci.org/dhii/structs.svg?branch=master)](https://travis-ci.org/dhii/structs)
[![Code Climate](https://codeclimate.com/github/dhii/structs/badges/gpa.svg)](https://codeclimate.com/github/dhii/structs)
[![Test Coverage](https://codeclimate.com/github/dhii/structs/badges/coverage.svg)](https://codeclimate.com/github/dhii/structs/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/structs/version)](https://packagist.org/packages/dhii/structs)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

Create immutable, type-safe, data-only classes

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

When extending a struct, you may override the `getPropTypes()` method to add new properties.

```php
class NamedImage extends Image
{
    public function getPropTypes() : array {
        $propTypes = parent::getPropTypes();
        $propTypes['name'] = Ty::string();

        return $propTypes;
    }
}
```

**Some advice**: Take care if altering the parent struct's existing prop types to make sure that new types are
covariant. And remember, removing or renaming props breaks [LSP][lsp].

## Property Types

| Type | Values |
|--------|------|
| `Ty::mixed()` | Any value |
| `Ty::bool()` | Booleans and [boolean-castable][bools] values |
| `Ty::int()` | Integers and [integer-castable][ints] values |
| `Ty::float()` | Floats and [float-castable][floats] values |
| `Ty::string()` | Strings, [string-castable][strings] values and objects that implement [`__toString()`][stringables] |
| `Ty::array()` | Array values |
| `Ty::arrayLike()` | Array values and [`ArrayAccess`][array-access] objects |
| `Ty::iterable()` | [Iterable values][iterables] |
| `Ty::object(...)` | Object values, with an optional parent class restrictions |
| `Ty::callable()` | [Callable values][callables] |
| `Ty::enum(...)` | Values that exist within a pre-defined set |
| `Ty::union(...)` | Values that match **at least one** type in a given set |
| `Ty::intersect(...)` | Values that match **all** of the types in a given set |
| `Ty::nullable(?)` | Values that match a given type, or `null` |
| `Ty::custom(?)` | Define a custom prop type |

Examples:

```php
use Dhii\Structs\Struct;
use Dhii\Structs\Ty;

class MyStruct extends Struct
{
    public function getPropTypes() : array {
        return [
            // Nullable object type
            'owner' => Ty::nullable(Ty::object(DateTime::class)),

            // Enum type (of strings)
            'day' => Ty::enum('sun', 'mon', 'tues', 'wed', 'thurs', 'fri', 'sat'),

            // Either an array or an object
            'config' => Ty::union(Ty::array(), Ty::object()),

            // An object that implements Traversable AND Serializable
            'list' => Ty::object(Traversable::class, Serializable::class),

            // A custom type
            'total' => Ty::custom(function ($value) {
                if (is_int($value)) {
                    return $value;
                }
                if (is_array($value)) {
                    return array_sum($value);
                }
                throw new TypeError('Property value must be int or array');
            })
        ];
    }
}
```

## Performance

Using a user-land replacement for a traditional class will naturally impact performance.

We tested our struct implementation against a traditional class with equivalent properties, corresponding getter
methods and a constructor.

* **Classes used:** [demo/comparison.php](demo/comparison.php)
* **Benchmark test:** [demo/benchmark.php](demo/benchmark.php)

The test consisted of constructing an instance of each and reading all of the properties in sequence. Below are the
average results after 100 tests were run on an ArchLinux machine with a Core i7-6700K CPU clocked at 4.4GHz.

**Results**:

| Num Props | Impact                  |
| ---------:|:------------------------|
| 10 props  | `0.0013` seconds slower |
| 5 props   | `0.0010` seconds slower |
| 3 props   | `0.0008` seconds slower |

These differences are, in our humble opinion, acceptable. You would need to construct at least a hundred struct
instances before a performance difference could be felt.

In contrast, the amount of code required to write a PHP class that guarantees type safety and is immutable grows
exponentially with the number of properties, whereas a struct sees only 1 added line per property (2 if you document
the property in the class docblock).

Ultimately, we are willing to sacrifice a very small amount of performance for added maintainability and peace of mind.
Maybe you'll agree with us ☺

[bools]: https://www.php.net/manual/en/language.types.boolean.php#language.types.boolean.casting
[ints]: https://www.php.net/manual/en/language.types.integer.php#language.types.integer.casting
[floats]: https://www.php.net/manual/en/language.types.float.php#language.types.float.casting
[strings]: https://www.php.net/manual/en/language.types.string.php#language.types.string.casting
[stringables]: https://www.php.net/manual/en/language.oop5.magic.php#object.tostring
[callables]: https://www.php.net/manual/en/function.is-callable.php
[iterables]: https://www.php.net/manual/en/language.types.iterable.php
[array-access]: https://www.php.net/manual/en/class.arrayaccess

[Dhii]: https://github.com/Dhii/dhii
[lsp]: https://en.wikipedia.org/wiki/Liskov_substitution_principle
