<?php

namespace Danek\Validator\Tests;

use Danek\Validator\Failure;
use Danek\Validator\Rule\AlphaRule;
use Danek\Validator\ValidationResult;
use PHPUnit\Framework\TestCase;

class ValidationResultTest extends TestCase
{
    public function testReturnsResultAndMessages()
    {
        $values = [
            'first_name' => 'test',
        ];

        $messages = [
            'first_name' => [
                AlphaRule::NOT_ALPHA => 'first name may only consist out of alphabetical characters',
            ],
        ];

        $failures = [
            new Failure(
                'first_name',
                AlphaRule::NOT_ALPHA,
                'first name may only consist out of alphabetical characters',
                []
            ),
        ];

        $result = new ValidationResult(false, $failures, $values);

        $this->assertFalse($result->isValid());
        $this->assertTrue($result->isNotValid());
        $this->assertEquals($messages, $result->getMessages());
        $this->assertEquals($values, $result->getValidatedValues());
    }
}
