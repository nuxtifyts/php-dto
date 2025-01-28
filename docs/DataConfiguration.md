Data Configuration
=

The library uses a configuration based approach to define, load and save data.
It uses a `DataConfiguration` object to define many things:

```php
use Nuxtifyts\PhpDto\Configuration\DataConfiguration;

$config = DataConfiguration::getInstance();
```

The function's signature is:

| Argument    | Type          | Description                                                                                     |
|-------------|---------------|-------------------------------------------------------------------------------------------------|
| config      | array \| null | The configuration array to load, by default it's `null`, which means switch to default configs. |
| forceCreate | bool          | If `true`, it will create a new instance of `DataConfiguration` even if it's already created.   |

If nothing is passed, it will be the equivalent of:

```php
DataConfiguration::getInstance([
    'normalizers' => [
        'baseNormalizers' => [
            JsonStringNormalizer::class,
            StdClassNormalizer::class,
            ArrayAccessNormalizer::class,
            ArrayNormalizer::class,
        ],
    ],
    
    'serializers' => [
        'baseSerializers' => [
            ArraySerializer::class,
            DataSerializer::class,
            DateTimeSerializer::class,
            BackedEnumSerializer::class,
            ScalarTypeSerializer::class,
        ]
    ],
    
    'validation' => [
        'ruleReferer' => ValidationRulesReferer::class,
        'validator' => Validator::class,
    ] 
])
```
