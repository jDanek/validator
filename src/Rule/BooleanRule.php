<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class BooleanRule extends Rule
{
    const NOT_BOOL = 'Boolean::NOT_BOOL';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_BOOL => '{{name}} must be either true or false',
    ];

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        return is_bool($value) || $this->addError(self::NOT_BOOL);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldBreakChainOnError(): bool
    {
        return true;
    }
}
