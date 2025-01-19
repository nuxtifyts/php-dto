<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation\Logical;

use Nuxtifyts\PhpDto\Exceptions\LogicalRuleException;
use Nuxtifyts\PhpDto\Validation\Contracts\RuleEvaluator;
use Nuxtifyts\PhpDto\Validation\Logic\AndRule;
use Nuxtifyts\PhpDto\Validation\Logic\LogicalRule;
use Nuxtifyts\PhpDto\Validation\Logic\OrRule;
use Nuxtifyts\PhpDto\Validation\Logic\SingularRule;
use Nuxtifyts\PhpDto\Validation\Rules\EmailRule;
use Nuxtifyts\PhpDto\Validation\Rules\NullableRule;
use Nuxtifyts\PhpDto\Validation\Rules\RequiredRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(SingularRule::class)]
#[CoversClass(LogicalRuleException::class)]
#[UsesClass(RequiredRule::class)]
#[UsesClass(OrRule::class)]
#[UsesClass(AndRule::class)]
#[UsesClass(OrRule::class)]
final class SingularLogicalRuleTest extends LogicalRuleTestCase
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
            'Will throw an exception when trying to add more than one rule' => [
                'logicalRuleClassString' => SingularRule::class,
                'ruleEvaluators' => [
                    RequiredRule::make(),
                    RequiredRule::make()
                ],
                'expectedCreateException' => LogicalRuleException::class,
                'valueToBeEvaluated' => 'value',
                'expectedResult' => false,
                'expectedValidationMessageTree' => []
            ],
            'Will be able to use validation rules' => [
                'logicalRuleClassString' => SingularRule::class,
                'ruleEvaluators' => [
                    $ruleA = RequiredRule::make()
                ],
                'expectedCreateException' => null,
                'valueToBeEvaluated' => 'value',
                'expectedResult' => true,
                'expectedValidationMessageTree' => [
                    'singular' => [
                        $ruleA->name => $ruleA->validationMessage()
                    ]
                ]
            ],
            'Will be able to resolve complex validations using OrRule and AndRule' => [
                'logicalRuleClassString' => SingularRule::class,
                'ruleEvaluators' => $ruleEvaluators = [
                    $orRule = new OrRule()
                        ->addRule(
                        $andRule = new AndRule()
                                ->addRule($requiredRule = RequiredRule::make())
                                ->addRule($emailRule = EmailRule::make())
                        )
                        ->addRule(
                            $nullableRule = NullableRule::make()
                        )
                ],
                'expectedCreateException' => null,
                'valueToBeEvaluated' => 'johndoe@example.test',
                'expectedResult' => true,
                'expectedValidationMessageTree' => $validationTree = [
                    'singular' => [
                        'or' => [
                            'and' => [
                                'required' => $requiredRule->validationMessage(),
                                'email' => $emailRule->validationMessage()
                            ],
                            'nullable' => $nullableRule->validationMessage()
                        ]
                    ]
                ]
            ],
            'Will be able to resolve complex validations using OrRule and AndRule 2' => [
                'logicalRuleClassString' => SingularRule::class,
                'ruleEvaluators' => $ruleEvaluators,
                'expectedCreateException' => null,
                'valueToBeEvaluated' => null,
                'expectedResult' => true,
                'expectedValidationMessageTree' => $validationTree,
            ],
            'Will be able to resolve complex validations using OrRule and AndRule 3' => [
                'logicalRuleClassString' => SingularRule::class,
                'ruleEvaluators' => $ruleEvaluators,
                'expectedCreateException' => null,
                'valueToBeEvaluated' => 1234.5,
                'expectedResult' => false,
                'expectedValidationMessageTree' => $validationTree,
            ]
        ];
    }
}
