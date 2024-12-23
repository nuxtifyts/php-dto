# PHP Pure Data objects

[//]: # ([![Latest Version on Packagist]&#40;https://img.shields.io/packagist/v/spatie/data-transfer-object.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/spatie/data-transfer-object&#41;)

[//]: # (![GitHub Tests Action Status]&#40;https://img.shields.io/github/workflow/status/spatie/data-transfer-object/run-tests?label=tests&#41;)

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

You can then create an instance of the class from a mixed value. The DTO will then attempt to hydrate the object with the given data.

```php
$data = [
    'firstName' => 'John',
    'lastName' => 'Doe',
    'fullName' => 'John Doe'
];

$user = UserData::from($data);
```

DTOs can also be serialized to an array:

```php

$user = new UserData('John', 'Doe');

$userData = $user->toArray();

// Or transform to a JSON string

$userData = $user->toJson();

```

Check out the [Quick start](https://github.com/nuxtifyts/php-dto/blob/main/docs/Quickstart.md) guide for more information.

### Note

This package was inspired from the [spatie/data-transfer-object](https://github.com/spatie/laravel-data) package.
The main thing that I tried to focus on when creating this package is to make it outside of Laravel ecosystem, 
meaning: no dependency on [illuminate/support](https://github.com/illuminate/support).

**In no way** I am trying to compare this package with the original one,
Clearly, the original package is more advanced and has more features than this one,
and if you are using Laravel, I highly recommend using the original package instead of this one.

### Requirements

- PHP 8.4 or higher
- That's it!

### Installation

You can install the package via composer:

```bash
composer require nuxtifyts/php-dto
```
