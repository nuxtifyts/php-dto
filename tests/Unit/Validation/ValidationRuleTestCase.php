<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation;

use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use Nuxtifyts\PhpDto\Validation\Rules\ValidationRule;
use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

abstract class ValidationRuleTestCase extends UnitCase
{
    abstract function validate_validation_message(): void;

    /** 
     * @param class-string<ValidationRule> $validationRuleClassString
     * @param ?array<string, mixed> $makeParams
     * @param ?class-string<ValidationRuleException> $expectedMakeException
     * @param mixed $valueToBeEvaluated
     * @param bool $expectedResult
     * 
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('data_provider')]
    public function will_be_able_to_use_rule(
        string $validationRuleClassString,
        ?array $makeParams,
        ?string $expectedMakeException,
        mixed $valueToBeEvaluated,
        bool $expectedResult
    ): void {
        if ($expectedMakeException) {
            self::expectException($expectedMakeException);
            $validationRuleClassString::make($makeParams);
            
            return;
        }

        $rule = $validationRuleClassString::make($makeParams);

        self::assertEquals(
            $expectedResult,
            $rule->evaluate($valueToBeEvaluated)
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
    abstract public static function data_provider(): array;
}
