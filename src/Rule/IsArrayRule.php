<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class IsArrayRule extends Rule
{
    const NOT_AN_ARRAY = 'IsArray::NOT_AN_ARRAY';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_AN_ARRAY => '{{name}} must be an array',
    ];

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        if (is_array($value)) {
            return true;
        }

        return $this->addError(self::NOT_AN_ARRAY);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldBreakChainOnError(): bool
    {
        return true;
    }
}
