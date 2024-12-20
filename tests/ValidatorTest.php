<?php

namespace Danek\Validator\Tests;

use Danek\Validator\Output\Structure;
use Danek\Validator\Rule;
use Danek\Validator\Rule\EmailRule;
use Danek\Validator\Rule\LengthBetweenRule;
use Danek\Validator\Rule\NotEmptyRule;
use Danek\Validator\Rule\RequiredRule;
use Danek\Validator\Tests\Support\Statement;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public function testCanOverwriteSpecificMessages()
    {
        $this->validator->required('foo');
        $this->validator->overwriteMessages([
            'foo' => [
                RequiredRule::NON_EXISTENT_KEY => 'This is my overwritten message. The key was "{{key}}".',
            ],
        ]);
        $result = $this->validator->validate([]);

        $this->assertFalse($result->isValid());
        $this->assertEquals(
            [
                'foo' => [
                    RequiredRule::NON_EXISTENT_KEY => 'This is my overwritten message. The key was "foo".',
                ],
            ],
            $result->getMessages()
        );
    }

    public function testOverwritingKeyWillReuseExistingChainAndTheLatterRequirednessIsUsed()
    {
        $this->validator->required('foo');
        $this->validator->optional('foo');

        $result = $this->validator->validate([]);

        $this->assertTrue($result->isValid());
    }

    public function testDefaultMessageOverwrites()
    {
        $this->validator->overwriteDefaultMessages([
            Rule\LengthRule::TOO_SHORT => 'this is my overwritten message. {{length}} is the length.',
        ]);
        $this->validator->required('first_name', 'Voornaam')->length(5);
        $result = $this->validator->validate(['first_name' => 'Rick']);

        $expected = [
            'first_name' => [
                Rule\LengthRule::TOO_SHORT => 'this is my overwritten message. 5 is the length.',
            ],
        ];

        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    public function testSpecificMessageWillHavePrecedenceOverDefaultMessage()
    {
        $this->validator->overwriteDefaultMessages([
            Rule\LengthRule::TOO_SHORT => 'This is overwritten globally.',
        ]);

        $this->validator->overwriteMessages([
            'first_name' => [
                Rule\LengthRule::TOO_SHORT => 'This is overwritten for first_name only.',
            ],
        ]);

        $this->validator->required('first_name')->length(5);

        $result = $this->validator->validate(['first_name' => 'Rick']);
        $this->assertFalse($result->isValid());

        $expected = [
            'first_name' => [
                Rule\LengthRule::TOO_SHORT => 'This is overwritten for first_name only.',
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    public function testReturnsValidatedValues()
    {
        $this->validator->required('first_name')->lengthBetween(2, 20);
        $this->validator->required('last_name')->lengthBetween(2, 60);
        $this->validator->optional('age')->between(18, 100);

        $result = $this->validator->validate([
            'first_name' => 'Berry',
            'last_name' => 'Langerak',
            'is_admin' => true,
            'age' => 42,
        ]);

        $expected = [
            'first_name' => 'Berry',
            'last_name' => 'Langerak',
            'age' => 42,
        ];

        $this->assertEquals($expected, $result->getValidatedValues());
    }

    public function testDoesNotReturnInvalidValues()
    {
        $this->validator->required('first_name')->lengthBetween(2, 20);
        $this->validator->required('last_name')->lengthBetween(2, 60);
        $this->validator->optional('age')->between(18, 100);
        $this->validator->optional('date')->datetime('Y-m-d');

        $result = $this->validator->validate([
            'first_name' => 'Berry',
            'last_name' => 'Langerak',
            'is_admin' => true,
            'date' => '01/01/1970',
        ]);

        $expected = [
            'first_name' => 'Berry',
            'last_name' => 'Langerak',
        ];

        $this->assertEquals($expected, $result->getValidatedValues());
    }

    public function testNoFalsePositivesForIssetOnFalse()
    {
        $this->validator->required('falsy_value');
        $result = $this->validator->validate([
            'falsy_value' => false,
        ]);

        $this->assertEquals([], $result->getMessages());
        $this->assertTrue($result->isValid());
    }

    public function testCanUseDotNotationToValidateInArrays()
    {
        $this->validator->required('user.contact.email')->email();

        $result = $this->validator->validate([
            'user' => [
                'contact' => [
                    'email' => 'example@particle-php.com',
                ],
            ],
        ]);

        $this->assertTrue($result->isValid());
    }

    public function testDotNotationIsAddedToMessagesVerbatim()
    {
        $this->validator->required('user.email');
        $result = $this->validator->validate([]);

        $expected = [
            'user.email' => [
                RequiredRule::NON_EXISTENT_KEY => 'user.email must be provided, but does not exist',
            ],
        ];

        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    public function testDotNotationIsAlsoUsedForOutputValueContainer()
    {
        $input = [
            'user' => [
                'email' => 'example@particle-php.com',
            ],
        ];
        $this->validator->required('user.email');
        $result = $this->validator->validate($input);
        $this->assertEquals($input, $result->getValidatedValues());
    }

    public function testDotNotationWillReturnTrueForNullRequiredValue()
    {
        $this->validator->required('user.email', 'user email address', true);

        $result = $this->validator->validate([
            'user' => [
                'email' => null,
            ],
        ]);

        $this->assertTrue($result->isValid());
    }

    /**
     * Bug fix test: Check if no notice is shown when no validation rules are configured.
     */
    public function testUnconfiguredValidatorWillNotShowNotice()
    {
        $this->assertTrue($this->validator->validate(['value' => 'yes'])->isValid());
    }

    public function testOutputWillGiveRepresentationOfInternalStructure()
    {
        $callable = function (Structure $structure) {
            $output = [];
            $subjects = $structure->getSubjects();
            foreach ($subjects as $subject) {
                foreach ($subject->getRules() as $rule) {
                    $output[$subject->getKey()][] = [
                        'rule' => $rule->getName(),
                        'messages' => $rule->getMessages(),
                        'parameters' => $rule->getParameters(),
                    ];
                }
            }

            return $output;
        };

        $this->validator->overwriteDefaultMessages([
            LengthBetweenRule::TOO_LONG => 'This is too long to output!',
        ]);

        $this->validator->overwriteMessages([
            'email' => [
                EmailRule::INVALID_FORMAT => 'This is not a valid email address',
            ],
        ]);

        $this->validator->required('email')->email();
        $this->validator->optional('firstname')->allowEmpty(true)->lengthBetween(0, 20);

        $definition = $this->validator->output($callable);

        $expected = [
            'email' => [
                [
                    'rule' => 'RequiredRule',
                    'messages' => [
                        RequiredRule::NON_EXISTENT_KEY => '{{key}} must be provided, but does not exist',
                    ],
                    'parameters' => [
                        'key' => 'email',
                        'name' => 'email',
                        'required' => true,
                        'callback' => null,
                    ],
                ],
                [
                    'rule' => 'NotEmptyRule',
                    'messages' => [
                        NotEmptyRule::EMPTY_VALUE => '{{name}} must not be empty',
                    ],
                    'parameters' => [
                        'key' => 'email',
                        'name' => 'email',
                        'allowEmpty' => false,
                        'callback' => null,
                    ],
                ],
                [
                    'rule' => 'EmailRule',
                    'messages' => [
                        EmailRule::INVALID_FORMAT => 'This is not a valid email address',
                    ],
                    'parameters' => [
                        'key' => 'email',
                        'name' => 'email',
                    ],
                ],
            ],
            'firstname' => [
                [
                    'rule' => 'RequiredRule',
                    'messages' => [
                        RequiredRule::NON_EXISTENT_KEY => '{{key}} must be provided, but does not exist',
                    ],
                    'parameters' => [
                        'key' => 'firstname',
                        'name' => 'firstname',
                        'required' => false,
                        'callback' => null,
                    ],
                ],
                [
                    'rule' => 'NotEmptyRule',
                    'messages' => [
                        NotEmptyRule::EMPTY_VALUE => '{{name}} must not be empty',
                    ],
                    'parameters' => [
                        'key' => 'firstname',
                        'name' => 'firstname',
                        'allowEmpty' => true,
                        'callback' => null,
                    ],
                ],
                [
                    'rule' => 'LengthBetweenRule',
                    'messages' => [
                        LengthBetweenRule::TOO_LONG => 'This is too long to output!',
                        'LengthBetween::TOO_SHORT' => '{{name}} must be {{min}} characters or longer',
                    ],
                    'parameters' => [
                        'key' => 'firstname',
                        'name' => 'firstname',
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $definition);
    }

    public function testOutputWillGiveStatementIfItImplementsToString()
    {
        $this->validator->required('foo')
            ->required(new Statement('is required', false))
            ->allowEmpty(new Statement('is empty allowed', false))
            ->callback(new Statement('callback content', false));

        $callback = function (Structure $structure) {
            $rules = $structure->getSubjects()[0]->getRules();

            $this->assertEquals('is required', $rules[0]->getParameters()['callback']);
            $this->assertEquals('is empty allowed', $rules[1]->getParameters()['callback']);
            $this->assertEquals('callback content', $rules[2]->getParameters()['callback']);
        };

        $this->validator->output($callback);
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
