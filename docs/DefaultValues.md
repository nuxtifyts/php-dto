Default Values
=

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

In this example, the `UserConfigDataFallbackResolver` would look like this:

```php
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\FallbackResolver\FallbackResolver;

class UserConfigDataFallbackResolver implements FallbackResolver
{
    /** 
     * @param array<string, mixed> $rawData 
     */
    public static function resolve(array $rawData, PropertyContext $property) : mixed
    {
        $email = $rawData['email'] ?? null;
        
        return match(true) {
            str_contains($email, 'example.com') => new UserConfigData(/** Admin configs */),
            default => new UserConfigData(/** User configs */)
        }
    }
}
```

> When using `DefaultsTo` attribute, priority is given to the attribute instead of the parameter's default value.

If ever needed to create a new instance of a DTO with complex default value, 
using the constructor is no longer possible, instead, you can make use of the 
`create` function provided by the DTO class.

Using the same example above, we can create a new instance of `User` with the default value for `config`:

```php
$user = UserData::create(
    firstName: 'John',
    lastName: 'Doe',
    email: 'johndoe@example.com'
);
```
