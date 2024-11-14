<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class NumericRule extends Rule
{
    const NOT_NUMERIC = 'Numeric::NOT_NUMERIC';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_NUMERIC => '{{name}} must be numeric',
    ];

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        if (is_numeric($value)) {
            return true;
        }
        return $this->addError(self::NOT_NUMERIC);
    }
}
