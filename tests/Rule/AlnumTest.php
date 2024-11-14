<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\AlphaNumRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class AlnumTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function getAlphanumericWithoutSpaces(): array
    {
        return [
            ['alphanumeric1337'],
            ['1337alphanumerictoo'],
        ];
    }

    public static function getAlphanumericWithSpaces(): array
    {
        return [
            ['alphanumeric 1337', AlphaNumRule::NOT_ALPHA_NUM],
            ['1337 this is alpha numeric', AlphaNumRule::NOT_ALPHA_NUM],
        ];
    }

    public static function getAlphanumericWithAccents(): array
    {
        return [
            ['Björk'],
        ];
    }

    /**
     * @dataProvider getAlphanumericWithoutSpaces
     * @param mixed $value
     */
    public function testReturnsTrueForValidValues($value)
    {
        $this->validator->required('first_name')->alphaNum();
        $result = $this->validator->validate(['first_name' => $value]);

        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider getAlphanumericWithSpaces
     * @param mixed $value
     */
    public function testReturnsTrueForValidValuesWithSpaces($value)
    {
        $this->validator->required('first_name')->alphaNum(true);
        $result = $this->validator->validate(['first_name' => $value]);

        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider getAlphanumericWithAccents
     * @param mixed $value
     */
    public function testReturnsTrueForDifferentAlphabets($value)
    {
        $this->validator->required('first_name')->alphaNum(true);
        $result = $this->validator->validate(['first_name' => $value]);

        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider getAlphanumericWithSpaces
     * @param mixed $value
     */
    public function testReturnsFalseForValuesWithSpaces($value, string $errorReason)
    {
        $this->validator->required('first_name')->alphaNum();
        $result = $this->validator->validate(['first_name' => $value]);

        $expected = ['first_name' => [$errorReason => $this->getMessage($errorReason)]];

        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            AlphaNumRule::NOT_ALPHA_NUM => 'first name may only consist out of numeric and alphabetic characters',
        ];

        return $messages[$reason];
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
