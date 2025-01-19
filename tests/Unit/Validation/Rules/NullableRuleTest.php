<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation\Rules;

use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Validation\Rules\NullableRule;
use Nuxtifyts\PhpDto\Validation\Rules\ValidationRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

#[CoversClass(NullableRule::class)]
final class NullableRuleTest extends ValidationRuleTestCase
{
    /** 
     *  @throws Throwable
     */
    #[Test]
    public function validate_validation_message(): void
    {
        self::assertEquals(
            'The :attribute field must be nullable.',
            NullableRule::make()->validationMessage()
        );
    }

    /** 
     *  @return array<string, array{
     *     validationRuleClassString: class-string<ValidationRule>,
     *     makeParams: ?array<string, mixed>,
     *     expectedMakeException: ?class-string<ValidationRuleException>,
     *     valueToBeEvaluated: mixed,
     *     expectedResult: bool
     * }>
     */
    public static function data_provider(): array
    {
        return [
            'Will return true if the value is null' => [
                'validationRuleClassString' => NullableRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => null,
                'expectedResult' => true,
            ],
            'Will return false if the value is different then null' => [
                'validationRuleClassString' => NullableRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'Something',
                'expectedResult' => false,
            ]
        ];
    }
}
