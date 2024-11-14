<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\EmailRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    /**
     * Returns a list of addresses considered valid.
     */
    public static function getValidAddresses(): array
    {
        return [
            ['berry+plus-sign@github.com.museum'],
            ['berry@githüb.com'],
            ['berry@github.com'],
            ['bërry@github.com'],
        ];
    }

    /**
     * Returns a list of addresses considered invalid.
     */
    public static function getInvalidAddresses(): array
    {
        return [
            ['berry'],
            ['not valid@"not valid"'],
        ];
    }

    /**
     * @dataProvider getValidAddresses
     * @param string $value
     */
    public function testReturnsTrueOnValidEmailaddresses(string $value)
    {
        $this->validator->required('email')->email();
        $result = $this->validator->validate(['email' => $value]);
        $this->assertTrue($result->isValid());
    }

    /**
     * @dataProvider getInvalidAddresses
     * @param mixed $value
     */
    public function testReturnsFalseOnInvalidEmailAddresses($value)
    {
        $this->validator->required('email')->email();
        $result = $this->validator->validate(['email' => $value]);
        $this->assertFalse($result->isValid());
        $expected = [
            'email' => [
                EmailRule::INVALID_FORMAT => 'email must be a valid email address',
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
