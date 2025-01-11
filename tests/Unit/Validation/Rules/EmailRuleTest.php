<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation\Rules;

use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Validation\Rules\EmailRule;
use Nuxtifyts\PhpDto\Validation\Rules\ValidationRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

#[CoversClass(EmailRule::class)]
final class EmailRuleTest extends ValidationRuleTestCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function validate_validation_message(): void
    {
        self::assertEquals(
            'The :attribute field must be a valid email address.',
            EmailRule::make()->validationMessage()
        );
    }

    /**
     * @return array<string, array{
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
            'Will evaluate false when value is not a string' => [
                'validationRuleClassString' => EmailRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'test1234',
                'expectedResult' => false
            ],
            'Will evaluate false when a string value is provided but it is not a valid email address' => [
                'validationRuleClassString' => EmailRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => false
            ],
            'Will evaluate true when a valid email address is provided' => [
                'validationRuleClassString' => EmailRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'johndoe@example.com',
                'expectedResult' => true,
            ],
        ];
    }
}
