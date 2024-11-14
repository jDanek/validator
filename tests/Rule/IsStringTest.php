<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\IsStringRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class IsStringTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function getInvalidStringValues(): array
    {
        return [
            [9000],
            [3.14],
            [true],
            [new \stdClass()],
        ];
    }

    public function testReturnsTrueOnValidString()
    {
        $value = 'foo';

        $this->validator->required('value')->string();

        $result = $this->validator->validate([
            'value' => $value,
        ]);

        $this->assertTrue($result->isValid());
    }

    /**
     * @dataProvider getInvalidStringValues
     *
     * @param mixed $value
     */
    public function testReturnsFalseOnInvalidString($value)
    {
        $this->validator->required('value')->string();

        $result = $this->validator->validate([
            'value' => $value,
        ]);

        $this->assertFalse($result->isValid());

        $expected = [
            'value' => [
                IsStringRule::NOT_A_STRING => $this->getMessage(IsStringRule::NOT_A_STRING),
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            IsStringRule::NOT_A_STRING => 'value must be a string',
        ];

        return $messages[$reason];
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
