<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\LessThanRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class LessThanTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public function testReturnsTrueForValuesLessThanMax()
    {
        $this->validator->required('number')->lessThan(5);
        $result = $this->validator->validate(['number' => 1]);

        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    public function testValidatesExclusiveByDefault()
    {
        $this->validator->required('number')->lessThan(5);
        $result = $this->validator->validate(['number' => 5]);

        $expected = [
            'number' => [
                LessThanRule::NOT_LESS_THAN => 'number must be less than 5',
            ],
        ];

        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    public function testReturnsFalseForValuesGreaterThanMax()
    {
        $this->validator->required('number')->lessThan(5);
        $result = $this->validator->validate(['number' => 10]);

        $expected = [
            'number' => [
                LessThanRule::NOT_LESS_THAN => $this->getMessage(LessThanRule::NOT_LESS_THAN),
            ],
        ];

        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            LessThanRule::NOT_LESS_THAN => 'number must be less than 5',
        ];

        return $messages[$reason];
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
