<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class IsFloatRule extends Rule
{
    const NOT_A_FLOAT = 'IsFloat::NOT_A_FLOAT';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_A_FLOAT => '{{name}} must be a float',
    ];

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        if (is_float($value)) {
            return true;
        }

        return $this->addError(self::NOT_A_FLOAT);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldBreakChainOnError(): bool
    {
        return true;
    }
}
