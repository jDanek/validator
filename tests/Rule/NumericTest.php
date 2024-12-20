<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\NumericRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class NumericTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function getValidNumericValues(): array
    {
        return [
            ['133.7'],
            [133.7],
            ['1337'],
            ['1211'],
            ['0'],
            [1231],
            [-12],
            ['-12'],
            [0xFF],
        ];
    }

    public static function getInvalidNumericValues(): array
    {
        return [
            ['a1211'],
            ['not even a number in sight!'],
        ];
    }

    /**
     * @dataProvider getValidNumericValues
     * @param mixed $value
     */
    public function testReturnsTrueOnValidNumeric($value)
    {
        $this->validator->required('number')->numeric();
        $result = $this->validator->validate(['number' => $value]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider getInvalidNumericValues
     * @param mixed $value
     */
    public function testReturnsFalseOnInvalidNumeric($value)
    {
        $this->validator->required('number')->numeric();
        $result = $this->validator->validate(['number' => $value]);

        $this->assertFalse($result->isValid());

        $expected = [
            'number' => [
                NumericRule::NOT_NUMERIC => $this->getMessage(NumericRule::NOT_NUMERIC),
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            NumericRule::NOT_NUMERIC => 'number must be numeric',
        ];

        return $messages[$reason];
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
