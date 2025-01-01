Name Mapper
=

Sometimes we could be expecting payload with different letter case or different naming convention. 
In such cases, we can use the `NameMapper` attribute to map the property to the correct key in the data array.

```php
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Attributes\Class\MapName;
use Nuxtifyts\PhpDto\Enums\LetterCase;

#[MapName(from: [LetterCase::KEBAB, LetterCase::SNAKE])]
final readonly class UserData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName
    ) {}
}
```

In the above example, passed data with keys `letter_case` and `letter-case` will be mapped to `letterCase` (By default),
and all of these keys will be transformed to the selected letter case.

```php
$user = UserData::from([ 'first-name' => 'John', 'last_name': 'Doe' ]);
```

> **Note:** The `MapName` attribute is applied on every key in the data array.

`MapName` attribute accepts these params: 

| Param  | Type                             | Description                      | Default           |
|--------|----------------------------------|----------------------------------|-------------------|
| `from` | `LetterCase`\|`list<LetterCase>` | List of letter cases to map from | -                 |
| `to`   | `LetterCase`                     | Letter case to map to            | LetterCase::CAMEL |
