Lazy Data
=

Out of the box, extending `Data` will give you the ability to create [Lazy objects](https://www.php.net/manual/en/language.oop5.lazy-objects.php).
You can achieve that by calling either `createLazy` or `createLazyUsing` methods, depending
on whether you want to pass properties from the get-go or not.

Let's take for example `UserData` class:

```php
use Nuxtifyts\PhpDto\Data;

final readonly class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $firstName,
        public string $lastName
    ) {}
}
```

We can create a lazy object like this:

```php
$user = UserData::createLazy(
    id: 1, 
    firstName: 'John', 
    lastName: 'Doe'
);
```

Or, if we have more complex logic to run before creating the object, we can do:

```php
// Supposedly, we know the user id.
$userId = 1;

$user = UserData::createLazyUsing(
    static function () use($userId): UserData {
        // Fetch user data from the database. then create the DTO.
        return UserData::from(UserModel::find($userId));
    }
)
```

The `createLazyUsing` method accepts a closure that returns the object. 
This closure will be called only once, and the object will be cached for future calls.

> For more information about lazy objects. Please refer to the [PHP documentation](https://www.php.net/manual/en/language.oop5.lazy-objects.php).

Lazy Data Attribute
-

Sometimes we may want to enable lazy data for a specific `Data` class.
In order to do that, we can user the `Lazy` attribute.

```php
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Attributes\Class\Lazy;

#[Lazy]
final readonly class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $firstName,
        public string $lastName
    ) {}
}
```

This will enable lazy data for all the "essential" functions that `Data` provides:
[create](https://github.com/nuxtifyts/php-dto/blob/main/docs/DefaultValues.md), 
[from](https://github.com/nuxtifyts/php-dto/blob/main/docs/Quickstart.md), 
[empty](https://github.com/nuxtifyts/php-dto/blob/main/docs/EmptyData.md)
and [with](https://github.com/nuxtifyts/php-dto/blob/main/docs/CloneableData.md).



