<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\LengthRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class LengthTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function getInvalidValues(): array
    {
        return [
            ['rick', LengthRule::TOO_SHORT],
            ['hendrik', LengthRule::TOO_LONG],
        ];
    }

    public static function getValidValues(): array
    {
        return [
            ['berry'],
            [12345], // integers are cast to strings
        ];
    }

    /**
     * @dataProvider getInvalidValues
     * @param mixed $value
     */
    public function testInvalidValuesWillReturnFalseAndLogError($value, string $error)
    {
        $this->validator->required('first_name')->length(5);
        $result = $this->validator->validate(['first_name' => $value]);

        $expected = ['first_name' => [$error => $this->getMessage($error)]];
        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            LengthRule::TOO_SHORT => 'first name is too short and must be 5 characters long',
            LengthRule::TOO_LONG => 'first name is too long and must be 5 characters long',
        ];
        return $messages[$reason];
    }

    /**
     * @dataProvider getValidValues
     * @param mixed $value
     */
    public function testValidValuesWillReturnTrue($value)
    {
        $this->validator->required('first_name')->length(5);
        $result = $this->validator->validate(['first_name' => $value]);
        $this->assertTrue($result->isValid());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
