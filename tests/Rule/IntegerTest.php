<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\IntegerRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function getValidNonStrictIntegerValues(): array
    {
        return [
            ['1337'],
            ['1211'],
            ['0'],
            [1231],
            [-12],
            ['-12'],
            [0xFF],
        ];
    }

    public static function getValidStrictIntegerValues(): array
    {
        return [
            [3],
            [-10],
            [0b111],
            [0x111],
        ];
    }

    public static function getInvalidNonStrictIntegerValues(): array
    {
        return [
            ['133.7'],
            ['a1211'],
        ];
    }

    public static function getInvalidStrictIntegerValues(): array
    {
        return [
            ['123'],
            ['987.3'],
            [828.3],
            ['a11'],
        ];
    }

    /**
     * @dataProvider getValidNonStrictIntegerValues
     * @param mixed $value
     */
    public function testReturnsTrueOnNonStrictValidInteger($value)
    {
        $this->validator->required('integer')->integer();
        $result = $this->validator->validate(['integer' => $value]);
        $this->assertTrue($result->isValid());
    }

    /**
     * @dataProvider getInvalidNonStrictIntegerValues
     * @param mixed $value
     */
    public function testReturnsFalseOnNonStrictInvalidIntegers($value)
    {
        $this->validator->required('integer')->integer();
        $result = $this->validator->validate(['integer' => $value]);
        $this->assertFalse($result->isValid());

        $expected = [
            'integer' => [
                IntegerRule::NOT_AN_INTEGER => $this->getMessage(IntegerRule::NOT_AN_INTEGER),
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            IntegerRule::NOT_AN_INTEGER => 'integer must be an integer',
        ];

        return $messages[$reason];
    }

    /**
     * @dataProvider  getValidStrictIntegerValues
     *
     * @param mixed $value
     */
    public function testReturnsTrueOnStrictValidInteger($value)
    {
        $this->validator->required('integer')->integer(true);
        $result = $this->validator->validate(['integer' => 3]);
        $this->assertTrue($result->isValid());
    }

    /**
     * @dataProvider getInvalidStrictIntegerValues
     *
     * @param mixed $value
     */
    public function testReturnsFalseOnStrictInvalidIntegers($value)
    {
        $this->validator->required('integer')->integer(true);
        $result = $this->validator->validate(['integer' => $value]);
        $this->assertFalse($result->isValid());

        $expected = [
            'integer' => [
                IntegerRule::NOT_AN_INTEGER => $this->getMessage(IntegerRule::NOT_AN_INTEGER),
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
