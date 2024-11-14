<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\LengthBetweenRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class LengthBetweenTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public function testReturnsTrueIfLengthIsExactlyMinOrMax()
    {
        $this->validator->required('first_name')->lengthBetween(2, 7);

        $result = $this->validator->validate(['first_name' => 'ad']);
        $this->assertTrue($result->isValid());

        $result = $this->validator->validate(['first_name' => 'Richard']);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    public function testReturnsTrueIfMaxIsNull()
    {
        $this->validator->required('password')->lengthBetween(2, null);
        $result = $this->validator->validate(['password' => str_repeat('foo', 100)]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    public function testReturnsFalseIfInvalid()
    {
        $this->validator->required('first_name')->lengthBetween(3, 6);
        $result = $this->validator->validate(['first_name' => 'ad']);

        $this->assertFalse($result->isValid());

        $expected = [
            'first_name' => [
                LengthBetweenRule::TOO_SHORT => $this->getMessage(LengthBetweenRule::TOO_SHORT),
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());

        $result = $this->validator->validate(['first_name' => 'Richard']);

        $this->assertFalse($result->isValid());
        $expected = [
            'first_name' => [
                LengthBetweenRule::TOO_LONG => $this->getMessage(LengthBetweenRule::TOO_LONG),
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            LengthBetweenRule::TOO_SHORT => 'first name must be 3 characters or longer',
            LengthBetweenRule::TOO_LONG => 'first name must be 6 characters or shorter',
        ];

        return $messages[$reason];
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
