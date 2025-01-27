Data Validation
=

Sometimes it's necessary to run some validations before creating your `Data` object. 
The library provides a way to do this by using a `RuleReferer`. for example: 

```php
use Nuxtifyts\PhpDto\Data;

final readonly class CoordinatesData extends Data
{
    public function __construct(
        private float $latitude,
        private float $longitude
    ) {
    }
}
```

This class will automatically add some validation for the `latitude` and `longitude` properties: 

```php
[
    'latitude' => 'float'
]
```
