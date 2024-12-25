Data Refiners
=

In the deserialization process, sometimes we may need to refine the data before it is passed to the deserializer.
This is where Data Refiners come in.

A pretty good example would be DateTimes. When attempting to create an instanceof DateTime, we may need to 
be aware of specific formats that the DateTime can be created from. 

By default, these are the DataRefiners that are available in the library:
- [DateTimeRefiner](#DateTimeRefiner) - Refines the data to a DateTimeImmutable instance depending on the format provided.

DateTimeRefiner
-

```php
use Nuxtifyts\PhpDto\Data;
use DateTimeImmutable;

final readonly class DateRangeData extends Data
{
    public function __construct(
        public ?DateTimeImmutable $start,
        public ?DateTimeImmutable $end
    ) {}
}
```

With this DTO, if we try to hydrate it with a custom format `'Y/m-d'`, it will fail.

```php
DateRangeData::from([
    'start' => '2023/01-12',
    'end' => '2023/01-14'
]);
```

To resolve this, we may need to specify a Data Refiner that will help deserialize the data.

```php
use Nuxtifyts\PhpDto\Data;
use DateTimeImmutable;
use Nuxtifyts\PhpDto\Attributes\Property\WithRefiner;
use Nuxtifyts\PhpDto\DataRefiners\DateTimeRefiner;

final readonly class DateRangeData extends Data
{
    public function __construct(
        #[WithRefiner(DateTimeRefiner::class, formats: 'Y/m-d')]
        public ?DateTimeImmutable $start,
        #[WithRefiner(DateTimeRefiner::class, formats: 'Y/m-d')]
        public ?DateTimeImmutable $end
    ) {}
}
```

With this, hydrating the DTO will be possible.

Creating a Custom Data Refiner
=

To create a custom Data Refiner, you need to implement the `DataRefiner` interface. for example suppose we
want to create a Data Refiner that will refine an object of class `CustomDate`:

```php
class CustomDate {
    public function __construct(
        private(set) int $year,
        private(set) int $month,
        private(set) int $day
    ) {}
    
    // ...
}
```

We can add the ability to hydrate a `DateTime` property from this class using a custom refiner like so:

```php
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\DataRefiners\DataRefiner;


class CustomDateRefiner implements DataRefiner
{
    public function refine(mixed $value, PropertyContext $property) : mixed
    {
        if ($value instanceof CustomDate) {
            return DateTimeImmutable::createFromFormat(
                format: 'Y-m-d',
                datetime: sprintf('%d-%d-%d', $value->year, $value->month, $value->day)
            );
        }
        
        return $value;
    }
}
```

Now we can use this refiner in our previous DTO:

```php
use Nuxtifyts\PhpDto\Data;
use DateTimeImmutable;
use Nuxtifyts\PhpDto\Attributes\Property\WithRefiner;
use Nuxtifyts\PhpDto\DataRefiners\DateTimeRefiner;

final readonly class DateRangeData extends Data
{
    public function __construct(
        #[WithRefiner(DateTimeRefiner::class, formats: 'Y/m-d')]
        #[WithRefiner(CustomDateRefiner::class)]
        public ?DateTimeImmutable $start,
        #[WithRefiner(CustomDateRefiner::class)]
        #[WithRefiner(DateTimeRefiner::class, formats: 'Y/m-d')]
        public ?DateTimeImmutable $end
    ) {}
}
```
