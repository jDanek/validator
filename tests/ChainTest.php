<?php

namespace Danek\Validator\Tests;

use Danek\Validator\Rule;
use Danek\Validator\Rule\BooleanRule;
use Danek\Validator\Rule\EachRule;
use Danek\Validator\Rule\EmailRule;
use Danek\Validator\Rule\GreaterThanRule;
use Danek\Validator\Rule\InArrayRule;
use Danek\Validator\Rule\IntegerRule;
use Danek\Validator\Rule\IsArrayRule;
use Danek\Validator\Rule\IsFloatRule;
use Danek\Validator\Rule\IsStringRule;
use Danek\Validator\Rule\LengthBetweenRule;
use Danek\Validator\Rule\RequiredRule;
use Danek\Validator\Tests\Support\CustomRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class ChainTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function provideBreakChainData(): array
    {
        return [
            'break boolean rule on error' => [
                new BooleanRule(),
                new InArrayRule([true, false]),
                [
                    'foo' => 'string',
                ],
                [
                    'foo' => [
                        BooleanRule::NOT_BOOL => 'foo must be either true or false',
                    ],
                ],
            ],
            'break integer rule on error' => [
                new IntegerRule(),
                new GreaterThanRule(10),
                [
                    'foo' => 'string',
                ],
                [
                    'foo' => [
                        IntegerRule::NOT_AN_INTEGER => 'foo must be an integer',
                    ],
                ],
            ],
            'break isArray rule on error' => [
                new IsArrayRule(),
                new EachRule(function ($v) {
                    /** @var Validator $v */
                    $v->required('bar')->email();
                }),
                [
                    'foo' => 'string',
                ],
                [
                    'foo' => [
                        IsArrayRule::NOT_AN_ARRAY => 'foo must be an array',
                    ],
                ],
            ],
            'break isFloat rule on error' => [
                new IsFloatRule(),
                new GreaterThanRule(20),
                [
                    'foo' => 'string',
                ],
                [
                    'foo' => [
                        IsFloatRule::NOT_A_FLOAT => 'foo must be a float',
                    ],
                ],
            ],
            'break isString rule on error' => [
                new IsStringRule(),
                new LengthBetweenRule(1, 3),
                [
                    'foo' => ['array-value'],
                ],
                [
                    'foo' => [
                        IsStringRule::NOT_A_STRING => 'foo must be a string',
                    ],
                ],
            ],
            'break required rule' => [
                new BooleanRule(),
                new InArrayRule([true, false]),
                [],
                [
                    'foo' => [
                        RequiredRule::NON_EXISTENT_KEY => 'foo must be provided, but does not exist',
                    ],
                ],
            ],
            'do not break boolean rule' => [
                new BooleanRule(),
                new InArrayRule([false]),
                [
                    'foo' => true,
                ],
                [
                    'foo' => [
                        InArrayRule::NOT_IN_ARRAY => 'foo must be in the defined set of values',
                    ],
                ],
            ],
            'do not break integer rule' => [
                new IntegerRule(),
                new GreaterThanRule(10),
                [
                    'foo' => 5,
                ],
                [
                    'foo' => [
                        GreaterThanRule::NOT_GREATER_THAN => 'foo must be greater than 10',
                    ],
                ],
            ],
            'do not break isArray rule' => [
                new IsArrayRule(),
                new EachRule(function (Validator $v) {
                    $v->required('bar')->email();
                }),
                [
                    'foo' => [
                        ['bar' => 'invalid@email'],
                    ],
                ],
                [
                    'foo.0.bar' => [
                        EmailRule::INVALID_FORMAT => 'bar must be a valid email address',
                    ],
                ],
            ],
            'do not break isFloat rule' => [
                new IsFloatRule(),
                new GreaterThanRule(20),
                [
                    'foo' => 5.00,
                ],
                [
                    'foo' => [
                        GreaterThanRule::NOT_GREATER_THAN => 'foo must be greater than 20',
                    ],
                ],
            ],
            'do not break isString rule' => [
                new IsStringRule(),
                new LengthBetweenRule(1, 3),
                [
                    'foo' => 'abcdefg',
                ],
                [
                    'foo' => [
                        LengthBetweenRule::TOO_LONG => 'foo must be 3 characters or shorter',
                    ],
                ],
            ],
        ];
    }

    public function testCanMountRulesOnChain()
    {
        $rule = new CustomRule();

        $this->validator->required('foo')->mount($rule);

        $result = $this->validator->validate(['foo' => 'not bar']);

        $expected = [
            'foo' => [
                CustomRule::NOT_BAR => 'foo must be equal to "bar"',
            ],
        ];

        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    /**
     * @dataProvider provideBreakChainData
     */
    public function testBreakChain(Rule $firstRule, Rule $secondRule, array $data, array $expected)
    {
        $this
            ->validator
            ->required('foo')
            ->mount($firstRule)
            ->mount($secondRule);

        $result = $this->validator->validate($data);

        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
