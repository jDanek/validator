<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class DigitsRule extends Rule
{
    const NOT_DIGITS = 'Digits::NOT_DIGITS';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_DIGITS => '{{name}} may only consist out of digits',
    ];

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        if (ctype_digit((string)$value)) {
            return true;
        }
        return $this->addError(self::NOT_DIGITS);
    }
}
