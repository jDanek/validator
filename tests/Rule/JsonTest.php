<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\JsonRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    /**
     * Returns a list of JSON strings considered valid.
     */
    public static function getValidJsonStrings(): array
    {
        return [
            ['{}'],
            ['[]'],
            ['{"a": "b", "c": "d"}'],
            ['{"a": null, "c": true}'],
            ['{"a": 9, "c": 9.99}'],
            ['9'],
            ['"json"'],
        ];
    }

    /**
     * Returns a list of JSON strings considered invalid.
     *
     * @return array
     */
    public static function getInvalidJsonStrings(): array
    {
        return [
            ['["a": "b"'],
            ["{'a': 'b'}"],
            ['json'],
            [9],
        ];
    }

    /**
     * @dataProvider getValidJsonStrings
     * @param mixed $value
     */
    public function testReturnsTrueOnValidJsonString($value)
    {
        $this->validator->required('json')->json();
        $result = $this->validator->validate(['json' => $value]);
        $this->assertTrue($result->isValid());
    }

    /**
     * @dataProvider getInvalidJsonStrings
     * @param mixed $value
     */
    public function testReturnsFalseOnInvalidJsonString($value)
    {
        $this->validator->required('json')->json();
        $result = $this->validator->validate(['json' => $value]);
        $this->assertFalse($result->isValid());
        $expected = [
            'json' => [
                JsonRule::INVALID_FORMAT => 'json must be a valid JSON string',
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
