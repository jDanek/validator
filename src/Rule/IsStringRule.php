<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class IsStringRule extends Rule
{
    const NOT_A_STRING = 'IsString::NOT_A_STRING';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_A_STRING => '{{name}} must be a string',
    ];

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        if (is_string($value)) {
            return true;
        }

        return $this->addError(self::NOT_A_STRING);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldBreakChainOnError(): bool
    {
        return true;
    }
}
