<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\IsFloatRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class IsFloatTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function getInvalidFloatValues(): array
    {
        return [
            ['foo'],
            [9000],
            ['6,6'],
            [0],
            [true],
            [new \stdClass()],
        ];
    }

    public function testReturnsTrueOnValidFloat()
    {
        $value = 3.14;

        $this->validator->required('value')->float();

        $result = $this->validator->validate([
            'value' => $value,
        ]);

        $this->assertTrue($result->isValid());
    }

    /**
     * @dataProvider getInvalidFloatValues
     *
     * @param mixed $value
     */
    public function testReturnsFalseOnInvalidFloat($value)
    {
        $this->validator->required('value')->float();

        $result = $this->validator->validate([
            'value' => $value,
        ]);

        $this->assertFalse($result->isValid());

        $expected = [
            'value' => [
                IsFloatRule::NOT_A_FLOAT => $this->getMessage(IsFloatRule::NOT_A_FLOAT),
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            IsFloatRule::NOT_A_FLOAT => 'value must be a float',
        ];

        return $messages[$reason];
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
