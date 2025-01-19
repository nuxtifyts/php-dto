<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation\Logical;

use Nuxtifyts\PhpDto\Exceptions\LogicalRuleException;
use Nuxtifyts\PhpDto\Validation\Contracts\RuleEvaluator;
use Nuxtifyts\PhpDto\Validation\Logic\LogicalRule;
use Nuxtifyts\PhpDto\Validation\Logic\OrRule;
use Nuxtifyts\PhpDto\Validation\Rules\NumericRule;
use Nuxtifyts\PhpDto\Validation\Rules\StringRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(OrRule::class)]
#[UsesClass(StringRule::class)]
#[UsesClass(NumericRule::class)]
final class OrRuleTest extends LogicalRuleTestCase
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
            'Will be able to use validation rules' => [
                'logicalRuleClassString' => OrRule::class,
                'ruleEvaluators' => [
                    $stringRule = StringRule::make(),
                    $numericRule = NumericRule::make()
                ],
                'expectedCreateException' => null,
                'valueToBeEvaluated' => 'string',
                'expectedResult' => true,
                'expectedValidationMessageTree' => [
                    'or' => [
                        $stringRule->name => $stringRule->validationMessage(),
                        $numericRule->name => $numericRule->validationMessage()
                    ]
                ]
            ],
            'Will be able to use validation rules 2' => [
                'logicalRuleClassString' => OrRule::class,
                'ruleEvaluators' => [
                    $stringRule,
                    $numericRule
                ],
                'expectedCreateException' => null,
                'valueToBeEvaluated' => 1234,
                'expectedResult' => true,
                'expectedValidationMessageTree' => [
                    'or' => [
                        $stringRule->name => $stringRule->validationMessage(),
                        $numericRule->name => $numericRule->validationMessage()
                    ]
                ]
            ],
            'Will be able to use validation rule 3' => [
                'logicalRuleClassString' => OrRule::class,
                'ruleEvaluators' => [
                    $numericRule
                ],
                'expectedCreateException' => null,
                'valueToBeEvaluated' => 1234.45,
                'expectedResult' => false,
                'expectedValidationMessageTree' => [
                    'or' => [
                        $numericRule->name => $numericRule->validationMessage()
                    ]
                ]
            ]
        ];
    }
}
