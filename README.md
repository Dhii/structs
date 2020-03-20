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
 * For IDE IntelliSense auto completion, declare the read-only properties in the class docblock.
 *
 * @property-read string $url
 * @property-read int $width
 * @property-read int $height
 */
class Image extends Struct
{
    // You can even add a custom constructor
    public function __construct(string $url, int $width, int $height) {
        parent::__construct(compact($url, $width, $height));
    }

    public function getPropTypes() : array
    {
        return [
            'url' => Ty::string(),
            'width' => Ty::int(),
            'height' => Ty::int(),
        ];
    }
}

$image = new Image('http://example.com/image.png', 800, 600);
```

You can then construct the strucaccess the properties using normal PHP object property read syntax:

```php
$image->url; // "http://example.com/image.png"
$image->width; // 800
$image->height; // 600
```

Structs can be copied using the `with()` method, which leaves the original struct unchanged:

```php
$flipedImage = $image->with([
    'width' => $image->height,
    'height' => $image->width
]);
```

[Dhii]: https://github.com/Dhii/dhii
