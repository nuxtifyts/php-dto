<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation\Logical;

use Nuxtifyts\PhpDto\Exceptions\LogicalRuleException;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use Nuxtifyts\PhpDto\Validation\Contracts\RuleEvaluator;
use Nuxtifyts\PhpDto\Validation\Logic\LogicalRule;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

abstract class LogicalRuleTestCase extends UnitCase
{
    /**
     * @param class-string<LogicalRule> $logicalRuleClassString
     * @param list<RuleEvaluator> $ruleEvaluators
     * @param ?class-string<LogicalRuleException> $expectedCreateException
     * @param array<string, mixed> $expectedValidationMessageTree
     *
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('data_provider')]
    public function will_be_able_to_use_logical_rules(
        string $logicalRuleClassString,
        array $ruleEvaluators,
        ?string $expectedCreateException,
        mixed $valueToBeEvaluated,
        bool $expectedResult,
        array $expectedValidationMessageTree
    ): void {
        if ($expectedCreateException) {
            self::expectException($expectedCreateException);
        }

        $logicalRule = new $logicalRuleClassString();

        foreach ($ruleEvaluators as $ruleEvaluator) {
            $logicalRule->addRule($ruleEvaluator);
        }

        if ($expectedCreateException) {
            return;
        }

        self::assertEquals(
            $expectedResult,
            $logicalRule->evaluate($valueToBeEvaluated)
        );

        self::assertEquals(
            $expectedValidationMessageTree,
            $logicalRule->validationMessageTree()
        );
    }

    /**
     * @return array<string, array{
     *     logicalRuleClassString: class-string<LogicalRule>,
     *     ruleEvaluators: list<RuleEvaluator>,
     *     expectedCreateException: ?class-string<LogicalRuleException>,
     *     valueToBeEvaluated: mixed,
     *     expectedResult: bool,
     *     expectedValidationMessageTree: array<string, mixed>
     * }>
     */
    abstract public static function data_provider(): array;
}
