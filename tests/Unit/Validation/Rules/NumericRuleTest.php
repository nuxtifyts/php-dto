<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation\Rules;

use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Validation\Rules\NumericRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(NumericRule::class)]
#[CoversClass(ValidationRuleException::class)]
#[UsesClass(Type::class)]
final class NumericRuleTest extends ValidationRuleTestCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function validate_validation_message(): void
    {
        self::assertEquals(
            'The :attribute field must be a valid integer.',
            NumericRule::make()->validationMessage()
        );

        self::assertEquals(
            'The :attribute field must be a valid float.',
            NumericRule::make(['type' => Type::FLOAT])->validationMessage()
        );
    }

    /**
     * @return array<string, array{
     *     validationRuleClassString: class-string<NumericRule>,
     *     makeParams: ?array<string, mixed>,
     *     expectedMakeException: ?class-string<ValidationRuleException>,
     *     valueToBeEvaluated: mixed,
     *     expectedResult: bool
     * }>
     */
    public static function data_provider(): array
    {
        return [
            'Will evaluate false when value is not a number' => [
                'validationRuleClassString' => NumericRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => false
            ],
            'Will evaluate true when an integer value is provided' => [
                'validationRuleClassString' => NumericRule::class,
                'makeParams' => ['type' => Type::INT],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 123,
                'expectedResult' => true
            ],
            'Will evaluate false when an integer value is provided but min is greater than the value' => [
                'validationRuleClassString' => NumericRule::class,
                'makeParams' => ['type' => Type::INT, 'min' => 124],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 123,
                'expectedResult' => false
            ],
            'Will evaluate false when an integer value is provided but max is less than the value' => [
                'validationRuleClassString' => NumericRule::class,
                'makeParams' => ['type' => Type::INT, 'max' => 122],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 123,
                'expectedResult' => false
            ],
            'Will evaluate true when a float value is provided' => [
                'validationRuleClassString' => NumericRule::class,
                'makeParams' => ['type' => Type::FLOAT],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 123.45,
                'expectedResult' => true
            ],
            'Will evaluate false when a float value is provided but min is greater than the value' => [
                'validationRuleClassString' => NumericRule::class,
                'makeParams' => ['type' => Type::FLOAT, 'min' => 123.46],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 123.45,
                'expectedResult' => false
            ],
            'Will evaluate false when a float value is provided but max is less than the value' => [
                'validationRuleClassString' => NumericRule::class,
                'makeParams' => ['type' => Type::FLOAT, 'max' => 123.44],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 123.45,
                'expectedResult' => false
            ],
            'Will throw an exception if an invalid type is provided' => [
                'validationRuleClassString' => NumericRule::class,
                'makeParams' => ['type' => Type::BOOLEAN],
                'expectedMakeException' => ValidationRuleException::class,
                'valueToBeEvaluated' => 123,
                'expectedResult' => false
            ],
        ];
    }
}
