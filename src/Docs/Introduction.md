Introduction
=

This package enabled the creation of data objects which can be used in various ways.
Using modern PHP syntax, it allows you to hydrate data from arrays, objects, and other data sources.
As well as carrying out the data, type validation and serialize the data for any purpose.

To create a `data` class, you will need to declare a `readonly` class that extends `Data` class.
Then you can define the properties of the class and their types.

```php
use Nuxtifyts\PhpDto\Data;

final readonly class UserData extends Data
{
    public string $fullName;

    public function __construct(
        public string $firstName,
        public stirng $lastName
    ) {
        $this->fullName = "$this->firstName $this->lastName";
    }
}
```

By extending from `Data` you enable the ability to serialize/deserialize a data object.

- Hydrating a data from a `mixed` value using normalizers
- Serializing a data from a data object to an array or JSON.

Checkout the [Quickstart](#) guide to get started with the package.
