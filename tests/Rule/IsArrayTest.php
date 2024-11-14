<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\IsArrayRule;
use Danek\Validator\Rule\NotEmptyRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class IsArrayTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function getValidArrayValues(): array
    {
        return [
            [[1, 2]],
            [['a' => 1, 'b' => 2]],
        ];
    }

    public static function getInvalidArrayValues(): array
    {
        return [
            ['abc'],
            [123],
            [123.45],
            [new \stdClass()],
        ];
    }

    /**
     * @dataProvider getValidArrayValues
     * @param mixed $value
     */
    public function testReturnsTrueOnValidArray($value)
    {
        $this->validator->required('array')->isArray();
        $result = $this->validator->validate(['array' => $value]);

        $this->assertTrue($result->isValid());
    }

    /**
     * @dataProvider getInvalidArrayValues
     * @param mixed $value
     */
    public function testReturnsFalseOnInvalidArrays($value)
    {
        $this->validator->required('array')->isArray();
        $result = $this->validator->validate(['array' => $value]);
        $this->assertFalse($result->isValid());

        $expected = [
            'array' => [
                IsArrayRule::NOT_AN_ARRAY => $this->getMessage(IsArrayRule::NOT_AN_ARRAY),
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            IsArrayRule::NOT_AN_ARRAY => 'array must be an array',
        ];

        return $messages[$reason];
    }

    public function testReturnsFalseOnEmptyArray()
    {
        $value = [];
        $this->validator->required('array')->isArray();
        $result = $this->validator->validate(['array' => $value]);
        $this->assertFalse($result->isValid());

        $expected = [
            'array' => [
                NotEmptyRule::EMPTY_VALUE => 'array must not be empty',
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
