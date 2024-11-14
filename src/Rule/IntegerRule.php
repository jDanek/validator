<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class IntegerRule extends Rule
{
    const NOT_AN_INTEGER = 'Integer::NOT_AN_INTEGER';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_AN_INTEGER => '{{name}} must be an integer',
    ];

    /** @var bool */
    private $strict;

    public function __construct(bool $strict = false)
    {
        $this->strict = $strict;
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        if ($this->strict && is_int($value)) {
            return true;
        }

        if (!$this->strict && false !== filter_var($value, FILTER_VALIDATE_INT)) {
            return true;
        }

        return $this->addError(self::NOT_AN_INTEGER);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldBreakChainOnError(): bool
    {
        return true;
    }
}
