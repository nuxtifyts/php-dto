Quickstart
=

In this quickstart guide, you will learn the most basic usage of this package.

First, you should [install](https://github.com/nuxtifyts/php-dto?tab=readme-ov-file#installation) the package using composer:

We will create a Todo application, where we need to manage a list of TODOs. so we can start
with creating a `Todo` data class.

```php
use Nuxtifyts\PhpDto\Data;

final readonly class Todo extends Data
{
    public function __construct(
        public string $title,
        public string $content,
        public Status $status,
        public ?DateTimeImmutable $dueDate
    ) {}
}
```

Extending from `Data` class is all you'll need to start using data transfer objects. 
You can define the properties of the class and their types.

In the above example, the `Status` a native `BackedEnum` class:

```php
enum Status: string
{
    case BACKLOG = 'backlog';
    case READY = 'ready';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
}
```

We can now create a new instance of the `Todo`:

```php
$todo = new Todo(
    title: 'Learn PHP DTO',
    content: 'Learn how to use PHP DTO',
    status: Status::READY,
    dueDate: new DateTimeImmutable()
)
```

The package allows you to hydrate these data objects from other types, for example an array:

```php
$todo = Todo::from([
    'title' => 'Learn PHP DTO',
    'content' => 'Learn how to use PHP DTO',
    'status' => 'ready',
    'dueDate' => '2025-01-01T00:00:00+00:00'
]);
```

You can also serialize the data object to an array, or a json string:

```php
// Serialize to an array
$todo->toArray();

// Serialize to a json string
json_encode($todo); // or
$todo->toJson();
```

Conclusion
-

This is the most basic usage of this package, more details on how to use the package 
can be found here:

- [Supported Types](https://github.com/nuxtifyts/php-dto/blob/main/src/Docs/SupportedTypes.md)
- [Normalizers](https://github.com/nuxtifyts/php-dto/blob/main/src/Docs/Normalizers.md)
- [Property Attributes (TBD)](#)
