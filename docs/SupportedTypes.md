Supported Types
=

This package bring support to the following types:

- [Scalar types](#scalar-types) - `string`, `int`, `float`, `bool`
- [DateTime types](#datetime-types) - `DateTime`, `DateTimeImmutable`
- [BackedEnum types](#backedenum-types) - `BackedEnum`
- [Data types](#data-types) - `Data`
- [Array types](#array-types) - `array`, `list`

It is also possible to use [Union types](#union-types) to define multiple types for a single property.

Scalar types
=

To declare a scalar type, simplify specify the type in the property declaration:

```php
use Nuxtifyts\PhpDto\Data;use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;

final readonly class Person extends Data
{
    public function __construct(
        public string $name,
        public int $age,
        public ?float $height,
        public bool $isStudent
    ) {}
}

$person = PersonData::from([
    'name' => 'John Doe',
    'age' => 25,
    'height' => null,
    'isStudent' => true
]);
```

DateTime types
=

To use `DateTimes` within your data class, you can go with either `DateTime` or `DateTimeImmutable`,
it is recommended to use `DateTimeImmutable` to keep a full immutability of the data object:

```php
use Nuxtifyts\PhpDto\Data;

final readonly class Person extends Data
{
    public function __construct(
        public string $name,
        public ?DateTimeImmutable $birthDate
    ) {}
}

$person = new Person(
    name: 'John Doe',
    birthDate: new DateTimeImmutable('1996-01-01')
);

$person2 = Person::from([
    'name' => 'John Doe',
    'birthDate' => null
]);
```

BackedEnum types
=

Using `BackedEnum` is a great way to define a set of constants that can be used as a type in your data class:

```php
use Nuxtifyts\PhpDto\Data;

final readonly class UserAccount extends Data
{
    public function __construct(
        public string $uuid,
        public Status $status
    ) {}
}

$userAccount = UserAccount::from([
    'uuid' => '123456',
    'status' => 'active'
]);
```

Data types
=

It is possible to use another data object within your data class:

```php
use Nuxtifyts\PhpDto\Data;use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;

final readonly class Person extends Data
{
    public function __construct(
        public string $name,
        public ?UserAccount $account
    ) {}
}

$person = Person::from([
    'name' => 'John Doe',
    'account' => [
        'uuid' => '123456',
        'status' => 'active'
    ]
]);

$person2 = PersonData::from([
    'name' => 'Jane Doe',
    'account' => null
]);
```

Array types
=

To declare an array type, you can use the `array` type and specify the type of 
elements using [PHP attributes](https://www.php.net/manual/en/language.attributes.overview.php).

The following attributes are available to use with array types:

- [ArrayOfScalarType](#array-of-scalar-type) - Elements of array could be of a scalar type.
- [ArrayOfBackedEnums](#array-of-backed-enums) - Elements of array could be of a `BackedEnum` type.
- [ArrayOfDateTimes](#array-of-datetime) - Elements of array could be of a `DateTime` type.
- [ArrayOfData](#array-of-data) - Elements of array could be of a `Data` type.

Details about these attributes:
- These attributes can be used on properties of type `array`.
- All of these attributes are repeatable on the same property.
- These attributes can be combined with each other.
- Array keys are kept when hydrating the data object. (See [This section](#array-keys))

Most of these attributes can repeatable to allow multiple types. and example would be
shown for `ArrayOfScalarType`.

Array of scalar type
-

To declare an array of scalar type, you can use the `ArrayOfScalarType` attribute:

```php
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfScalarTypes;use Nuxtifyts\PhpDto\Data;use Nuxtifyts\PhpDto\Enums\Property\Type;

final readonly class Person extends Data
{
    /**
     * @param list<string> $nicknames
     */
    public function __construct(
        public string $name,
        #[ArrayOfScalarTypes(Type::STRING)]
        public array $nicknames
    ) {}
}
```

You can also specify multiple scalar types if needed:

```php
final readonly class Person extends Data
{
    /**
     * @param list<int|string> $favoriteWordsOrNumbers
     */
    public function __construct(
        public string $name,
        #[ArrayOfScalarTypes(Type::INT, Type::STRING)]
        public array $favoriteWordsOrNumbers
    ) {}
}
```

Or by repeating the attribute

```php
final readonly class Person extends Data
{
    /**
     * @param list<int|string> $favoriteWordsOrNumbers
     */
    public function __construct(
        public string $name,
        #[ArrayOfScalarTypes(Type::INT)]
        #[ArrayOfScalarTypes(Type::STRING)]
        public array $favoriteWordsOrNumbers
    ) {}
}
```

When not specifying the type, all the scalar types are allowed:

```php
final readonly class ScalarTypes extends Data
{
    /** 
     * @param list<int|float|bool|string> $arrayOfScalarTypes
     */
    public function __construct(
        #[ArrayOfScalarTypes]
        public array $arrayOfScalarTypes
    ) {}
}
```

Array of backed enums
-

Using `ArrayOfBackedEnum` attribute, you can declare an array of `BackedEnum` type:

```php
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfBackedEnums;use Nuxtifyts\PhpDto\Data;

final readonly class User extends Data
{
    /** 
     * @param list<Status> $statuses
     */
    public function __construct(
        public string $name,
        #[ArrayOfBackedEnums(HappyStatus::class, AngryStatus::class)]
        public array $statuses
    ) {}
}
```

Array of DateTime
-

To declare an array of `DateTime` type, you can use the `ArrayOfDateTimes` attribute:

```php
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfDateTimes;

final readonly class User extends Data
{
    /** 
     * @param list<DateTimeImmutable> $dates
     */
    public function __construct(
        public string $name,
        #[ArrayOfDateTimes(DateTimeImmutable::class)]
        public array $recentLoginDates
    ) {}
}
```

Array of Data
-

To declare an array of `Data` type, you can use the `ArrayOfData` attribute:

```php
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfData;use Nuxtifyts\PhpDto\Data;

final readonly class UserGroup extends Data
{
    /** 
     * @param list<User> $members
     */
    public function __construct(
        public string $name,
        #[ArrayOfData(User::class)]
        public array $members
    ) {}
}
```

It is possible to use multiple types of data objects in the array:

```php
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfData;use Nuxtifyts\PhpDto\Data;

final readonly class UserGroup extends Data
{
    /** 
     * @param list<User|Admin> $members
     */
    public function __construct(
        public string $name,
        #[ArrayOfData(User::class)]
        #[ArrayOfData(Admin::class)]
        public array $members
    ) {}
}
```

Or by using one single attribute:

```php
final readonly class UserGroup extends Data
{
    /** 
     * @param list<User|Admin> $members
     */
    public function __construct(
        public string $name,
        #[ArrayOfData(User::class, Admin::class)]
        public array $members
    ) {}
}
```


Array keys
-

Array keys are kept where hydrating the data object, or when serializing:

```php
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfData;use Nuxtifyts\PhpDto\Data;

final readonly class UserGroup extends Data
{
    /** 
     * @param array<array-key, User> $members
     */
    public function __construct(
        public string $name,
        #[ArrayOfData(User::class, Admin::class)]
        public array $members
    ) {}
}
```

When hydrating the data object, the array keys are kept:

```php
UserGroupData::from([
    'name' => 'Developers',
    'members' => [
        'john' => [
            'name' => 'John Doe',
            'account' => [
                'uuid' => '123456',
                'status' => 'active'
            ]
        ],
        'jane' => [
            'name' => 'Jane Doe',
            'account' => null
        ]
    ]
]);
```

When serializing the data object, the array keys are kept:

```php
$userGroup->toArray();
```

And the output would be:

```php
[
    'name' => 'Developers',
    'members' => [
        'john' => [
            'name' => 'John Doe',
            'account' => [
                 'uuid' => '123456',
                 'status' => 'active'
            ]
        ],
        'jane' => [
            'name' => 'Jane Doe',
            'account' => null
        ]
    ]
]
```

Union types
=

It is possible to define multiple types for a single property in the data class:

```php
use Nuxtifyts\PhpDto\Data;

final readonly class Person extends Data
{
    public function __construct(
        public string|int $id,
        public DateTimeImmutable|string|null $createdAt,
        public YesOrNoBackedEnum|bool $isStudent,
        public ?AdminAccount|UserAccount $account
    ) {}
}
```

The exact same rules apply for array as well:

```php
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfData;use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfScalarTypes;use Nuxtifyts\PhpDto\Data;use Nuxtifyts\PhpDto\Enums\Property\Type;

final readonly class UserGroup extends Data
{
    /** 
     * @param list<User|Admin|string|int> $users 
     */
    public function __construct(
        public string $name;
        #[ArrayOfScalarTypes(Type::INT, Type::STRING)]
        #[ArrayOfData(User::class, Admin::class)]
        public array $users;
    ) {}
}
```
