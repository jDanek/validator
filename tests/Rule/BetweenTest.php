<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\BetweenRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class BetweenTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public function testReturnsTrueForValuesBetweenMinAndMax()
    {
        $this->validator->required('number')->between(1, 10);
        $result = $this->validator->validate(['number' => 5]);

        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    public function testValidatesInclusiveByDefault()
    {
        $this->validator->required('number')->between(1, 10);
        $result = $this->validator->validate(['number' => 1]);

        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    public function testReturnsFalseForValuesNotBetweenMinAndMaxLowerError()
    {
        $this->validator->required('number')->between(1, 10);
        $result = $this->validator->validate(['number' => 0]);

        $expected = [
            'number' => [
                BetweenRule::TOO_SMALL => $this->getMessage(BetweenRule::TOO_SMALL),
            ],
        ];
        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            BetweenRule::TOO_SMALL => 'number must be greater than or equal to 1',
            BetweenRule::TOO_BIG => 'number must be less than or equal to 10',
        ];

        return $messages[$reason];
    }

    public function testReturnsFalseForValuesNotBetweenMinAndMaxUpperError()
    {
        $this->validator->required('number')->between(1, 10);
        $result = $this->validator->validate(['number' => 11]);

        $expected = [
            'number' => [
                BetweenRule::TOO_BIG => $this->getMessage(BetweenRule::TOO_BIG),
            ],
        ];
        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    public function testReturnsTrueIfMaxIsNull()
    {
        $this->validator->required('password')->lengthBetween(2, null);
        $result = $this->validator->validate(['password' => str_repeat('foo', 100)]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
