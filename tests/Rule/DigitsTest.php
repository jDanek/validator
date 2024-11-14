<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\DigitsRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class DigitsTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function getNotOnlyDigitValues(): array
    {
        return [
            ['133.7'],
            ['a1211'],
            ['-12'],
        ];
    }

    public function testReturnsTrueOnOnlyDigitCharacters()
    {
        $this->validator->required('digits')->digits();
        $result = $this->validator->validate(['digits' => '123456789']);
        $this->assertTrue($result->isValid());
    }

    /**
     * @dataProvider getNotOnlyDigitValues
     * @param mixed $value
     */
    public function testReturnsFalseOnNonDigitCharacters($value)
    {
        $this->validator->required('digits')->digits();
        $result = $this->validator->validate(['digits' => $value]);
        $this->assertFalse($result->isValid());

        $expected = [
            'digits' => [
                DigitsRule::NOT_DIGITS => $this->getMessage(DigitsRule::NOT_DIGITS),
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            DigitsRule::NOT_DIGITS => 'digits may only consist out of digits',
        ];

        return $messages[$reason];
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
