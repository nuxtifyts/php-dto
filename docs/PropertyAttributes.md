Property Attributes
=

In order to provide more functionality to your DTOs, you can use the following attributes:
- [Computed](#Computed) - To define a property that is computed from other properties.
- [Aliases](#Aliases) - To define aliases for a property.

Computed
-

Sometimes, we may need to specify that a property is computed, meaning that it is derived from other properties. 
This can be done using the `Computed` attribute.

```php
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Attributes\Property\Computed;

final readonly class Person extends Data
{
    #[Computed]
    public string $fullName;

    public function __construct(
        public string $firstName,
        public string $lastName
    ) {
        $this->fullName = "$this->firstName $this->lastName";
    }
}
```

This will make the DTO aware of the `fullName` property, and it will not be serialized or deserialized.

Aliases
-

Sometimes, we may need to specify that a property can be hydrated from multiple keys in the data array.
This can be done using the `Aliases` attribute.

```php
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Attributes\Property\Aliases;

final readonly class Person extends Data
{
    public function __construct(
        #[Aliases('first_name')]
        public string $firstName,
        #[Aliases('last_name')]
        public string $lastName   
    ) {}
}
```

This will make it possible to hydrate properties from multiple array keys.
