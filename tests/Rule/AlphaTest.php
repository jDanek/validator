<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\AlphaRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class AlphaTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function getAlphaWithoutSpaces(): array
    {
        return [
            ['onlyalphabet'],
            ['alphabetagamma'],
        ];
    }

    public static function getAlphaWithSpaces(): array
    {
        return [
            ['alpha checks for alphabetical characters', AlphaRule::NOT_ALPHA],
            ['this is alpha numeric too', AlphaRule::NOT_ALPHA],
        ];
    }

    public static function getAlphaWithAccents(): array
    {
        return [
            ['Björk'],
        ];
    }

    /**
     * @dataProvider getAlphaWithoutSpaces
     * @param mixed $value
     */
    public function testReturnsTrueForValidValues($value)
    {
        $this->validator->required('first_name')->alpha();
        $result = $this->validator->validate(['first_name' => $value]);

        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider getAlphaWithSpaces
     * @param mixed $value
     */
    public function testReturnsTrueForValidValuesWithSpaces($value)
    {
        $this->validator->required('first_name')->alpha(true);
        $result = $this->validator->validate(['first_name' => $value]);

        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider getAlphaWithAccents
     * @param mixed $value
     */
    public function testReturnsTrueForDifferentAlphabets($value)
    {
        $this->validator->required('first_name')->alpha(true);
        $result = $this->validator->validate(['first_name' => $value]);

        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider getAlphaWithSpaces
     * @param mixed $value
     */
    public function testReturnsFalseForValuesWithSpaces($value, string $errorReason)
    {
        $this->validator->required('first_name')->alpha();
        $result = $this->validator->validate(['first_name' => $value]);

        $expected = ['first_name' => [$errorReason => $this->getMessage($errorReason)]];

        $this->assertFalse($result->isValid());
        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            AlphaRule::NOT_ALPHA => 'first name may only consist out of alphabetic characters',
        ];

        return $messages[$reason];
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
