Property Attributes
=

In order to provide more functionality to your DTOs, you can use the following attributes:
- [Computed](#Computed) - To define a property that is computed from other properties.
- [Aliases](#Aliases) - To define aliases for a property.
- [DefaultsUsing](#DefaultsUsing) - To define a default value for a property using a fallback resolver.
- [CipherTarget](#CipherTarget) - To define a property that should be encrypted/decrypted.

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
        #[Aliases('last_name', 'family_name')]
        public string $lastName   
    ) {}
}
```

This will make it possible to hydrate properties from multiple array keys.

CipherTarget
-

Sometimes, we may need to specify that some properties are considered sensitive, and should be
handled carefully, especially when saving it. 

For this we can use encryption/decryption using the `CipherTarget` attribute.

```php
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfData;
use Nuxtifyts\PhpDto\Attributes\Property\CipherTarget;

final readonly class User extends Data
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

DefaultsUsing
-

Sometimes, we may need to specify that a property has a default value, 
we can achieve that using plain PHP for some property types but not all of them.

```php
use Nuxtifyts\PhpDto\Data;

final readonly class User extends Data
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
that's where `DefaultsUsing` attribute comes in.

```php
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Attributes\Property\DefaultsUsing;

final readonly class User extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        #[DefaultsUsing(UserTypeFallbackResolver::class)]
        public UserType $type,
        #[DefaultsUsing(UserConfigDataFallbackResolver::class)]
        public UserConfigData $config,
    ) {}
}
```

TODO - Add example of fall back resolver code
