<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation;

use Throwable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Nuxtifyts\PhpDto\Validation\Rules\StringRule;
use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Tests\Unit\Validation\ValidationRuleTestCase;

#[CoversClass(StringRule::class)]
#[CoversClass(ValidationRuleException::class)]
final class StringRuleTest extends ValidationRuleTestCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function validate_validation_message(): void
    {
        $rule = StringRule::make();

        self::assertEquals(
            'The :attribute field must be a valid string.',
            $rule->validationMessage()
        );
    }

    /**
     * @return array<string, array{
     *     validationRuleClassString: class-string<StringRule>,
     *     makeParams: ?array<string, mixed>,
     *     expectedMakeException: ?class-string<ValidationRuleException>,
     *     valueToBeEvaluated: mixed,
     *     expectedResult: bool
     * }>
     */
    public static function data_provider(): array
    {
        return [
            'Will evaluate false when value is not a string' => [
                'validationRuleClassString' => StringRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 123,
                'expectedResult' => false
            ],
            'Will evaluate true when a string value is provided' => [
                'validationRuleClassString' => StringRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => true
            ],
            'Will evaluate false when a string value is provided but minLen is greater than the length of the string' => [
                'validationRuleClassString' => StringRule::class,
                'makeParams' => ['minLen' => 5],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => false
            ],
            'Will evaluate true when a string value is provided and minLen is less than the length of the string' => [
                'validationRuleClassString' => StringRule::class,
                'makeParams' => ['minLen' => 3],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => true
            ],
            'Will evaluate false when a string value is provided but maxLen is less than the length of the string' => [
                'validationRuleClassString' => StringRule::class,
                'makeParams' => ['maxLen' => 3],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => false
            ],
            'Will evaluate true when a string value is provided and maxLen is greater than the length of the string' => [
                'validationRuleClassString' => StringRule::class,
                'makeParams' => ['maxLen' => 5],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => true
            ],
            'Will evaluate false when a string value is provided but minLen is greater than the length of the string and maxLen is less than the length of the string' => [
                'validationRuleClassString' => StringRule::class,
                'makeParams' => ['minLen' => 5, 'maxLen' => 10],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => false
            ],
            'Will evaluate true when a string value is provided and minLen is less than the length of the string and maxLen is greater than the length of the string' => [
                'validationRuleClassString' => StringRule::class,
                'makeParams' => ['minLen' => 3, 'maxLen' => 5],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => true
            ],
            'Will throw an exception if minLen is less than 0' => [
                'validationRuleClassString' => StringRule::class,
                'makeParams' => ['minLen' => -1],
                'expectedMakeException' => ValidationRuleException::class,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => false
            ],
            'Will throw an exception if maxLen is less than minLen' => [
                'validationRuleClassString' => StringRule::class,
                'makeParams' => ['minLen' => 5, 'maxLen' => 3],
                'expectedMakeException' => ValidationRuleException::class,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => false
            ],
        ];
    }
}
