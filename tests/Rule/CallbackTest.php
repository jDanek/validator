<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Exception\InvalidValueException;
use Danek\Validator\Rule\CallbackRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class CallbackTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public function testReturnsTrueWhenCallbackReturnsTrue()
    {
        $this->validator->required('first_name')->callback(function ($value) {
            return $value === 'berry';
        });

        $result = $this->validator->validate(['first_name' => 'berry']);
        $this->assertTrue($result->isValid());
    }

    public function testReturnsFalseAndLogsErrorWhenCallbackReturnsFalse()
    {
        $this->validator->required('first_name')->callback(function ($value) {
            return $value !== 'berry';
        });

        $result = $this->validator->validate(['first_name' => 'berry']);
        $this->assertFalse($result->isValid());

        $expected = [
            'first_name' => [
                CallbackRule::INVALID_VALUE => $this->getMessage(CallbackRule::INVALID_VALUE),
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            CallbackRule::INVALID_VALUE => 'first name is invalid',
        ];

        return $messages[$reason];
    }

    public function testCanLogDifferentErrorMessageByThrowingException()
    {
        $this->validator->required('first_name')->callback(function ($value) {
            if ($value !== 'berry') {
                throw new InvalidValueException(
                    'This is my error',
                    'Callback::CUSTOM'
                );
            }
            return true;
        });

        $result = $this->validator->validate(['first_name' => 'bill']);
        $this->assertFalse($result->isValid());

        $expected = [
            'first_name' => [
                'Callback::CUSTOM' => 'This is my error',
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    public function testCanReadTheContextOfValidation()
    {
        $this->validator->required('first_name')->callback(function ($value, $context) {
            return $context['last_name'] === 'Langerak' && $value === 'Berry';
        });

        $result = $this->validator->validate(['first_name' => 'Berry', 'last_name' => 'Langerak']);
        $this->assertTrue($result->isValid());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
