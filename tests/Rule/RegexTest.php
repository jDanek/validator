<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\RegexRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public function testReturnsTrueWhenMatchesRegex()
    {
        $this->validator->required('first_name')->regex('/^berry$/i');
        $result = $this->validator->validate(['first_name' => 'Berry']);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    public function testReturnsFalseOnNoMatch()
    {
        $this->validator->required('first_name')->regex('~this wont match~');
        $result = $this->validator->validate(['first_name' => 'Berry']);
        $this->assertFalse($result->isValid());
        $expected = [
            'first_name' => [
                RegexRule::NO_MATCH => 'first name is invalid',
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
