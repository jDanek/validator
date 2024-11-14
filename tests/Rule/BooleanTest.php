<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\BooleanRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function getTestValuesAndResults(): array
    {
        return [
            [true, true],
            [false, true],
            ["true", false],
            ["yes", false],
            [1, false],
            [0, false],
        ];
    }

    /**
     * @param mixed $value
     * @dataProvider getTestValuesAndResults
     */
    public function testReturnsTrueOnlyOnValidBools($value, bool $expected)
    {
        $this->validator->required('active')->bool();
        $result = $this->validator->validate(['active' => $value]);
        $this->assertEquals($expected, $result->isValid());

        if ($expected === false) {
            $this->assertEquals(
                $this->getMessage(BooleanRule::NOT_BOOL),
                $result->getMessages()['active'][BooleanRule::NOT_BOOL]
            );
        }
    }

    private function getMessage(string $reason): string
    {
        $messages = [
            BooleanRule::NOT_BOOL => 'active must be either true or false',
        ];

        return $messages[$reason];
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
