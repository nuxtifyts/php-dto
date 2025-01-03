Property Attributes
=

In order to provide more functionality to your DTOs, you can use the following attributes:
- [Computed](#Computed) - To define a property that is computed from other properties.
- [Hidden](#Hidden) - To define a property that should not be serialized.
- [Aliases](#Aliases) - To define aliases for a property.
- [DefaultsTo](#DefaultsTo) - To define a default value for a property using a fallback resolver.
- [CipherTarget](#CipherTarget) - To define a property that should be encrypted/decrypted.

Computed
-

Sometimes, we may need to specify that a property is computed, meaning that it is derived from other properties. 
This can be done using the `Computed` attribute.

```php
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Attributes\Property\Computed;

final readonly class PersonData extends Data
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

Hidden
-

Sometimes, we may need to specify that a property should not be serialized.

This can be done using the `Hidden` attribute.

```php
use Nuxtifyts\PhpDto\Attributes\Property\Hidden;
use Nuxtifyts\PhpDto\Data;

final readonly class PersonData extends Data
{
    public function __construct(
        public string $firstName,
        #[Hidden]
        public string $lastName
    ) {}
} 
```

When serializing the DTO, the `lastName` property will not be included in the output.

Aliases
-

Sometimes, we may need to specify that a property can be hydrated from multiple keys in the data array.
This can be done using the `Aliases` attribute.

```php
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Attributes\Property\Aliases;

final readonly class PersonData extends Data
{
    public function __construct(
        #[Aliases('first_name')]
        public string $firstName,
        #[Aliases('last_name', 'family_name')]
        public string $lastName   
    ) {}
}
```

This will make it possible to hydrate properties from multiple array keys.

> **Note:** Sometimes, we may want to apply the `Aliases` attribute to the whole class,
> in case we want to transform letter cases of all the keys in data array.
> In such cases, we can use the [MapName](https://github.com/nuxtifyts/php-dto/blob/main/docs/NameMapper.md)
> attribute.

CipherTarget
-

Sometimes, we may need to specify that some properties are considered sensitive, and should be
handled carefully, especially when saving it. 

For this we can use encryption/decryption using the `CipherTarget` attribute.

```php
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfData;
use Nuxtifyts\PhpDto\Attributes\Property\CipherTarget;

final readonly class UserData extends Data
{
    /** 
     * @param list<UserConfigData> $userConfigs
     */
    public function __construct(
        #[ArrayOfData(UserConfigData::class)]
        #[CipherTarget(
            secret: 'user-configs-secret-key', // By default, it uses the class name
            encoded: true // By default, it does not perform encoding
        )]
        public array $userConfigs
    ) {}
}

```

it is also possible to specify a custom DataCipher for the property,
the new class should implement the `Nuxtifyts\PhpDto\DataCipher` interface.

```php

use Nuxtifyts\PhpDto\DataCiphers\DataCipher;

class CustomDataCipher implements DataCipher
{
    // Implement the interface
}
```

Then you can specify the custom DataCipher in the `CipherTarget` attribute.

```php
public function __construct(
    #[CipherTarget(
        dataCipherClass: CustomDataCipher::class,
    )]
    public UserConfigData $userConfig
) {}
```

DefaultsTo
-

Sometimes, we may need to specify that a property has a default value, 
we can achieve that using plain PHP for some property types but not all of them.

```php
use Nuxtifyts\PhpDto\Data;

final readonly class UserData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public UserType $type = UserType::DEFAULT,
        public UserConfigData $config,
    ) {}
}
```

On the other hand, if we want to specify, for example, a default value for UserType depending 
on the provided email address, or if you want to provide a default value for complex data such as
`UserConfigData` which is another DTO, there is no way to do it using plain PHP, 
that's where `DefaultsTo` attribute comes in.

```php
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Attributes\Property\DefaultsTo;

final readonly class UserData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        #[DefaultsTo(UserType::DEFAULT)]
        public UserType $type,
        #[DefaultsTo(UserConfigDataFallbackResolver::class)]
        public UserConfigData $config,
    ) {}
}
```

The `DefaultsTo` attribute provides the ability to specify default values for complex types, 
such as DateTimes and DTOs.

For more details checkout the [DefaultValues](https://github.com/nuxtifyts/php-dto/blob/main/docs/DefaultValues.md) 
guide.
