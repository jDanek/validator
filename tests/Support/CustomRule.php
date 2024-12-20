<?php

namespace Danek\Validator\Tests\Support;

use Danek\Validator\Rule;

class CustomRule extends Rule
{
    const NOT_BAR = 'CustomRule::NOT_BAR';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_BAR => '{{key}} must be equal to "bar"',
    ];

    /**
     * Validates if the value is equal to "bar".
     *
     * @param mixed $value
     */
    public function validate($value): bool
    {
        if ($value !== 'bar') {
            return $this->addError(self::NOT_BAR);
        }
        return true;
    }
}
