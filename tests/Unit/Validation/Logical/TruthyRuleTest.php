<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation\Logical;

use Nuxtifyts\PhpDto\Exceptions\LogicalRuleException;
use Nuxtifyts\PhpDto\Validation\Contracts\RuleEvaluator;
use Nuxtifyts\PhpDto\Validation\Logic\LogicalRule;
use Nuxtifyts\PhpDto\Validation\Logic\TruthyRule;
use Nuxtifyts\PhpDto\Validation\Rules\StringRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(TruthyRule::class)]
#[CoversClass(LogicalRuleException::class)]
#[UsesClass(StringRule::class)]
final class TruthyRuleTest extends LogicalRuleTestCase
{
    /**
     * @return array<string, array{
     *     logicalRuleClassString: class-string<LogicalRule>,
     *     ruleEvaluators: list<RuleEvaluator>,
     *     expectedCreateException: ?class-string<LogicalRuleException>,
     *     valueToBeEvaluated: mixed,
     *     expectedResult: bool,
     *     expectedValidationMessageTree: array<string, mixed>
     * }>
     *
     * @throws Throwable
     */
    public static function data_provider(): array
    {
        return [
            'Will always return true when validating' => [
                'logicalRuleClassString' => TruthyRule::class,
                'ruleEvaluators' => [],
                'expectedCreateException' => null,
                'valueToBeEvaluated' => 'string',
                'expectedResult' => true,
                'expectedValidationMessageTree' => []
            ],
            'Will throw an exception if trying to add a nested rule' => [
                'logicalRuleClassString' => TruthyRule::class,
                'ruleEvaluators' => [
                    StringRule::make(),
                ],
                'expectedCreateException' => LogicalRuleException::class,
                'valueToBeEvaluated' => 1234,
                'expectedResult' => true,
                'expectedValidationMessageTree' => []
            ]
        ];
    }
}
