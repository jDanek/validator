<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\NotEmptyRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class NotEmptyTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function notEmptyValues(): array
    {
        return [
            [false],
            [true],
            ['string'],
            [0],
            [0.00],
            [[1]],
        ];
    }

    public static function emptyValues(): array
    {
        return [
            [null],
            [''],
            [[]],
        ];
    }

    /**
     * @dataProvider notEmptyValues
     */
    public function testReturnsTrueOnNonEmptyValues($value)
    {
        $this->validator->optional('foo', 'foo', false);
        $result = $this->validator->validate(['foo' => $value]);

        $this->assertTrue($result->isValid());
    }

    /**
     * @dataProvider emptyValues
     */
    public function testReturnsFalseOnEmptyValues($value)
    {
        $this->validator->optional('foo', 'foo', false);
        $result = $this->validator->validate(['foo' => $value]);

        $this->assertFalse($result->isValid());
        $this->assertArrayHasKey(NotEmptyRule::EMPTY_VALUE, $result->getMessages()['foo']);
    }

    public function testBreaksChainOnAllowedEmptyValues()
    {
        $this->validator->required('foo', 'foo', true)->length(5);

        $result = $this->validator->validate([
            'foo' => null,
        ]);

        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    public function testAllowEmptyCanBeConditional()
    {
        $this->validator->required('first_name', 'first name', true)->allowEmpty(function ($values) {
            return $values['foo'] !== 'bar';
        });

        $result = $this->validator->validate(['foo' => 'bar', 'first_name' => '']);

        $this->assertFalse($result->isValid());
        $this->assertEquals(
            [
                'first_name' => [
                    NotEmptyRule::EMPTY_VALUE => 'first name must not be empty',
                ],
            ],
            $result->getMessages()
        );

        $result = $this->validator->validate(['foo' => 'not bar!', 'first_name' => '']);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
