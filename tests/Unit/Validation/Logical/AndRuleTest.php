<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation\Logical;

use Nuxtifyts\PhpDto\Exceptions\LogicalRuleException;
use Nuxtifyts\PhpDto\Validation\Contracts\RuleEvaluator;
use Nuxtifyts\PhpDto\Validation\Logic\AndRule;
use Nuxtifyts\PhpDto\Validation\Logic\LogicalRule;
use Nuxtifyts\PhpDto\Validation\Rules\EmailRule;
use Nuxtifyts\PhpDto\Validation\Rules\StringRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(AndRule::class)]
#[UsesClass(StringRule::class)]
#[UsesClass(EmailRule::class)]
final class AndRuleTest extends LogicalRuleTestCase
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
                'logicalRuleClassString' => AndRule::class,
                'ruleEvaluators' => [
                    $stringRule = StringRule::make(),
                    $emailRule = EmailRule::make()
                ],
                'expectedCreateException' => null,
                'valueToBeEvaluated' => 'string',
                'expectedResult' => false,
                'expectedValidationMessageTree' => [
                    'and' => [
                        $stringRule->name => $stringRule->validationMessage(),
                        $emailRule->name => $emailRule->validationMessage()
                    ]
                ]
            ],
            'Will be able to use validation rules 2' => [
                'logicalRuleClassString' => AndRule::class,
                'ruleEvaluators' => [
                    $stringRule = StringRule::make(),
                    $emailRule = EmailRule::make()
                ],
                'expectedCreateException' => null,
                'valueToBeEvaluated' => 'johndoe@example.test',
                'expectedResult' => true,
                'expectedValidationMessageTree' => [
                    'and' => [
                        $stringRule->name => $stringRule->validationMessage(),
                        $emailRule->name => $emailRule->validationMessage()
                    ]
                ]
            ]
        ];
    }
}
