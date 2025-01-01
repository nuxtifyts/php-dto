Normalizers
=

When trying to hydrate a data object using the `from` function, the package will use normalizers to convert the passed
data into (preferable) an `array`/`ArrayAccess`.

By default, there are 4 normalizers:

- **JsonStringNormalizer** will cast json string.
- **StdClassNormalizer** will cast stdObject.
- **ArrayAccessNormalizer** will cast ArrayAccess.
- **ArrayNormalizer** will cast array.

> **Note:** In order to adjust there default normalizers, for example, you don't need
> to use the `StdClassNormalizer` and you want to simply remove it, you need 
> to [configure](https://github.com/nuxtifyts/php-dto/blob/main/docs/DataConfiguration.md)
> the `DataConfiguration` object.

Custom normalizers:
=

Custom normalizers can be added if needed: To do this, you will need to extend the `Normalizer` class
and implement the `normalize` method.

For example: we have a custom class called: `Goal`: 

```php
class Goal
{
    public function __construct(
        public string $summary,
        public string $description,
        public DateTimeImmutable $dueDate
    ) {
    }
}
```

And we have our `TodoData` data class: 

```php
use Nuxtifyts\PhpDto\Data;

final readonly class TodoData extends Data
{
    public function __construct(
        public string $title,
        public string $content,
        public Status $status,
        public ?DateTimeImmutable $dueDate
    ) {
    }
}
```

And we want to hydrate instances of this class to using a normalizer. first, we need to create
a normalizer class, we'll call it `GoalTodoNormalizer`:

```php
use Nuxtifyts\PhpDto\Normalizers\Normalizer;

final readonly class GoalTodoNormalizer extends Normalizer
{
    public function normalize(): array|false
    {
        if (!$this->value instanceof Goal) {
            return false;
        }
        
        return [
            'title' => $this->value->summary,
            'content' => $this->value->description,
            'status' => 'ready',
            'dueDate' => $this->value->dueDate->format(DateTimeInterface::ATOM)
        ];   
    }
}
```

Next step is to add this new normalizer to the todo class, either using 
an attribute:

```php
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Attributes\Class\WithNormalizer;

#[WithNormalizer(GoalTodoNormalizer::class)]
final readonly class TodoData extends Data
{
    // ...
}
```

Or using a method:

```php
use Nuxtifyts\PhpDto\Data;

final readonly class TodoData extends Data
{
    // ...
    
    protected static function normalizers() : array{
        return GoalTodoNormalizer::class;
    }
}
```

This will add the `GoalTodoNormalizer` on top of the default normalizers. Now it'll be possible to hydrate
a `TodoData` instance from a `Goal` instance.

```php
$goal = new Goal(
    title: 'Learn PHP DTO',
    description: 'Learn how to use PHP DTO',
    dueDate: new DateTimeImmutable()
);

$todo = TodoData::from($goal);
```
