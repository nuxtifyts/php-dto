Empty Data
=

Sometimes we may need to create a fresh instance of a DTO without any data, 
and by default `Data` classes have the ability to create an `"empty"` instance:

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
    case DONE = 'done';
    case CANCELED = 'canceled';
}
```

By calling the `empty()` method, we can create a new instance of the `TodoData` class with all properties set to `null`:

```php
$emptyTodo = TodoData::empty();
```

> **Note:** This is really useful with [Cloneable Data](https://github.com/nuxtifyts/php-dto/blob/main/docs/CloneableData.md)

The `$emptyTodo` variable will contain the following data:

```
[
  'title' => '',
  'comtent' => '',
  'status' => Status::DEFAULT,
  'dueDate' => null
]
```

This is useful when we want to gradually fill in the data of a DTO instance, 
here is a list of the empty values for each type: 

- `NULL`: `null` (Null takes priority over everything)
- `STRING`: `''`
- `INT`: `0`
- `FLOAT`: `0.0`
- `BOOLEAN`: `false`
- `ARRAY`: `[]` (Any type of array will default to an empty one)
- `DATETIME`: New instance of DateTime/DateTimeImmutable
- `BACKEDENUM`: First case of the enum
