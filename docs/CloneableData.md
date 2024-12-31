Cloneable Data
= 

Sometimes we may want to alter the data of a `Data` object (Partially or completely).
And since `Data` objects are immutable by default, we can't change the data directly.

To solve this, we can use the `with` function that will return a new instance of the `Data` object with the new data.
Let take the `TodoData` class as an example:

```php
use Nuxtifyts\PhpDto\Data;
use DateTimeImmutable;

final readonly class TodoData extends Data
{
    public function __construct(
        public string $title,
        public string $content,
        public Status $status,
        public ?DateTimeImmutable $dueDate
    ) {}
}
```

The `Status` enum is defined as follows:

```php
enum Status: string
{
    case DEFAULT = 'default';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
}
```

Using `with` function, we can easily create new instances of the `TodoData` class with the new data:

```php
$emptyTodo = TodoData::empty();

// ...
 
$todo = $emptyTodo->with(
    title: 'Learn PHP DTO',
    content: 'Learn how to use PHP DTO',
    status: Status::IN_PROGRESS
);

// ...

$todoWithDueDate = $todo->with(
    dueDate: new DateTimeImmutable('2025-01-06')
);
```

> **Note:** We are using the `empty` method 
> from [Empty Data](https://github.com/nuxtifyts/php-dto/blob/main/docs/EmptyData.md)
> here

> **Important:** `emptyTodo`, `todo` and `todoWithDueDate` are all different instances.

Computed properties
-

When cloning a `Data` object, computed properties are automatically updated with the new data.

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
        $this->fullName = $this->firstName . ' ' . $this->lastName;
    }
}
```

For example: 

```php
$johnDoe = new PersonData(firstName: 'John', lastName: 'Doe');

$janeDoe = $johnDoe->with(firstName: 'Jane');

$janeDoe->fullName; // 'Jane Doe'
```

Normalizers
-

When cloning a `Data` object, normalizers that are typically used when hydrating a `Data` object
using `from` method are also used.

This will allow the ability to pass `json` data, `ArrayAccess` or `stdClass` objects for example to the `with` method.
If a custom normalizer is implemented for the `Data` class, it can be used as well.

```php
$johnDoe = new PersonDaa('John', 'Doe');

$janeDoe = $johnDoe->with('{"firstName": "Jane"}');

$janeDoe->fullName; // 'Jane Doe'
```

Using an `stdClass` object:

```php
$object = new stdClass();
$object->firstName = 'Jake';

$jakeDoe = $janeDoe->with($object);

$jakeDoe->fullName; // 'Jake Doe'
```
