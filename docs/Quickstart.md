Quickstart
=

In this quickstart guide, you will learn the most basic usage of this package.

First, you should [install](https://github.com/nuxtifyts/php-dto?tab=readme-ov-file#installation) the package using composer:

We will create a Todo application, where we need to manage a list of TODOs. so we can start
with creating a `TodoData` data class.

```php
use Nuxtifyts\PhpDto\Data;

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

We can now create a new instance of the `TodoData`:

```php
$todo = new TodoData(
    title: 'Learn PHP DTO',
    content: 'Learn how to use PHP DTO',
    status: Status::READY,
    dueDate: new DateTimeImmutable()
)
```

The package allows you to hydrate these data objects from other types, for example an array:

```php
$todo = TodoData::from([
    'title' => 'Learn PHP DTO',
    'content' => 'Learn how to use PHP DTO',
    'status' => 'ready', // Or Status::READY
    'dueDate' => '2025-01-01'
]);
```

You can also serialize the data object to an array, or a json string:

```php
// Serialize to an array
$todo->toArray();

// Serialize to a json string
$todo->toJson();
```

Conclusion
-

This is the most basic usage of this package, more details on how to use the package 
can be found here:

- [Supported Types](https://github.com/nuxtifyts/php-dto/blob/main/docs/SupportedTypes.md)
- [Normalizers](https://github.com/nuxtifyts/php-dto/blob/main/docs/Normalizers.md)
- [Property Attributes](https://github.com/nuxtifyts/php-dto/blob/main/docs/PropertyAttributes.md)
- [Name Mapper](https://github.com/nuxtifyts/php-dto/blob/main/docs/NameMapper.md)
- [Data Refiners](https://github.com/nuxtifyts/php-dto/blob/main/docs/DataRefiners.md)
- [Empty Data](https://github.com/nuxtifyts/php-dto/blob/main/docs/EmptyData.md)
- [Cloneable Data](https://github.com/nuxtifyts/php-dto/blob/main/docs/CloneableData.md)
- [Lazy Data](https://github.com/nuxtifyts/php-dto/blob/main/docs/LazyData.md)
- [Data Configuration](https://github.com/nuxtifyts/php-dto/blob/main/docs/DataConfiguration.md)
