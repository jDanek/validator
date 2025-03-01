<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\DateTimeRule;
use Danek\Validator\Validator;
use DateTime;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public function testRespectsFormatIfPassed()
    {
        $this->validator->required('time')->datetime('H:i');
        $result = $this->validator->validate(['time' => '18:00']);

        $this->assertEquals([], $result->getMessages());
        $this->assertTrue($result->isValid());

        $result = $this->validator->validate(['time' => (new DateTime())->format('Y-m-d H:i:s')]);

        $this->assertFalse($result->isValid());
        $expected = [
            'time' => [
                DateTimeRule::INVALID_VALUE => 'time must be a valid date',
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    public function testWillTakeManyFormatsIfNoFormatPassed()
    {
        $this->validator->required('time')->datetime();

        $result = $this->validator->validate(['time' => '18:00']);
        $this->assertTrue($result->isValid());

        $result = $this->validator->validate(['time' => '2015-03-29 16:11:09']);
        $this->assertTrue($result->isValid());

        $this->validator->validate(['time' => '29-03-2015 16:11:09']);
        $this->assertTrue($result->isValid());
    }

    public function testReturnsFalseOnUnparsableDate()
    {
        $this->validator->required('time')->datetime();
        $result = $this->validator->validate(['time' => 'This is not a date. Not even close.']);

        $this->assertFalse($result->isValid());
        $expected = [
            'time' => [
                DateTimeRule::INVALID_VALUE => 'time must be a valid date',
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    /**
     * @link https://github.com/particle-php/Validator/issues/31
     */
    public function testReturnsFalseOnParsableButValidFormat()
    {
        $this->validator->required('date')->datetime('Ymd');
        $result = $this->validator->validate([
            'date' => '12111978',
        ]);

        $this->assertFalse($result->isValid());
        $expected = [
            'date' => [
                DateTimeRule::INVALID_VALUE => 'date must be a valid date',
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    /**
     * @link https://github.com/particle-php/Validator/issues/59
     */
    public function testCheckForFormatRespect()
    {
        $this->validator->required('date')->datetime('Ymd');
        $result = $this->validator->validate(
            [
                'date' => '2015125',
            ]
        );

        // should fail because Ymd expects 20151205 instead of 2015125
        $this->assertFalse($result->isValid());
        $expected = [
            'date' => [
                DateTimeRule::INVALID_VALUE => 'date must be a valid date',
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    public function testCheckForValidDateWillAcceptBothStringsAsIntegers()
    {
        $this->validator->required('timestamp')->datetime('U');
        $result = $this->validator->validate([
            'timestamp' => (int)(new \DateTime())->format('U'),
        ]);

        $this->assertTrue($result->isValid());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    private function getMessage(string $reason): string
    {
        $messages = [
        ];

        return $messages[$reason];
    }
}
