<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\GreaterThanRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class GreaterThanTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public function testReturnsTrueForValuesGreaterThanMin()
    {
        $this->validator->required('number')->greaterThan(1);
        $result = $this->validator->validate(['number' => 5]);

        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    public function testValidatesExclusiveByDefault()
    {
        $this->validator->required('number')->greaterThan(1);
        $result = $this->validator->validate(['number' => 1]);

        $expected = [
            'number' => [
                GreaterThanRule::NOT_GREATER_THAN => 'number must be greater than 1',
            ],
        ];

        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    public function testReturnsFalseForValuesLessThanMin()
    {
        $this->validator->required('number')->greaterThan(1);
        $result = $this->validator->validate(['number' => 0]);

        $expected = [
            'number' => [
                GreaterThanRule::NOT_GREATER_THAN => $this->getMessage(GreaterThanRule::NOT_GREATER_THAN),
            ],
        ];

        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            GreaterThanRule::NOT_GREATER_THAN => 'number must be greater than 1',
        ];

        return $messages[$reason];
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
