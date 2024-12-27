<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Attributes;

use Nuxtifyts\PhpDto\Attributes\Property\CipherTarget;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfData;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfScalarTypes;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\DataCiphers\CipherConfig;
use Nuxtifyts\PhpDto\DataCiphers\DefaultDataCipher;
use Nuxtifyts\PhpDto\Exceptions\DataCipherException;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\Pipes\DecipherDataPipe;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

#[CoversClass(CipherTarget::class)]
#[CoversClass(DefaultDataCipher::class)]
#[CoversClass(CipherConfig::class)]
#[CoversClass(PropertyContext::class)]
#[CoversClass(DecipherDataPipe::class)]
#[CoversClass(DataCipherException::class)]
final class CipherTargetTest extends UnitCase
{
    /**
     * @param array<string, mixed> $expectedCipheredProperties
     *
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('data_encryption_data_provider')]
    public function will_perform_data_encryption_on_selected_properties(
        Data $data,
        array $expectedCipheredProperties
    ): void {
        $serialized = $data->jsonSerialize();

        $deserialized = $data::from($serialized);

        foreach ($expectedCipheredProperties as $propertyName => $value) {
            self::assertEquals($value, $deserialized->{$propertyName});
        }
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_an_exception_when_trying_to_decrypt_invalid_data(): void
    {
        $object = new readonly class ($apiKey = 'apiKey') extends Data {
            public function __construct(
                #[CipherTarget(
                    dataCipherClass: DefaultDataCipher::class,
                    secret: 'secret',
                    encoded: true
                )]
                public string $apiKey
            ) {
            }
        };

        self::expectException(DeserializeException::class);

        $object::from([
            'apiKey' => 'unencryptedData'
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function data_encryption_data_provider(): array
    {
        return [
            'Will encrypt scalar type data' => [
                'data' => new readonly class ($apiKey = 'apiKey') extends Data {
                    public function __construct(
                        #[CipherTarget(
                            dataCipherClass: DefaultDataCipher::class,
                            secret: 'secret',
                            encoded: true
                        )]
                        public string $apiKey
                    ) {
                    }
                },
                'expectedCipheredProperties' => [
                    'apiKey' => $apiKey
                ]
            ],
            'Will encrypt other non scalar type data' => [
                'data' => new readonly class (['apiKey1', 'apiKey2']) extends Data {
                    /**
                     * @param list<string> $apiKeys
                     */
                    public function __construct(
                        #[ArrayOfScalarTypes]
                        #[CipherTarget(
                            dataCipherClass: DefaultDataCipher::class,
                            secret: 'secret',
                            encoded: true
                        )]
                        public array $apiKeys
                    ) {
                    }
                },
                'expectedCipheredProperties' => [
                    'apiKeys' => [
                        'apiKey1',
                        'apiKey2'
                    ]
                ]
            ],
            'Will encrypt complex data' => [
                'data' => new readonly class ([
                    $johnDoe = new PersonData('John', 'Doe')    ,
                    $janeDoe = new PersonData('Jane', 'Doe')
                ]) extends Data {
                    /**
                     * @param list<PersonData> $admins
                     */
                    public function __construct(
                        #[ArrayOfData(PersonData::class)]
                        #[CipherTarget(
                            dataCipherClass: DefaultDataCipher::class,
                            secret: 'secret',
                            encoded: true
                        )]
                        public array $admins
                    ) {
                    }
                },
                'expectedCipheredProperties' => [
                    'admins' => [
                        $johnDoe,
                        $janeDoe
                    ]
                ]
            ]
        ];
    }
}
