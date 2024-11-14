<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class GreaterThanRule extends Rule
{
    const NOT_GREATER_THAN = 'GreaterThan::NOT_GREATER_THAN';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_GREATER_THAN => '{{name}} must be greater than {{min}}',
    ];

    /**
     * @var int|float
     */
    protected $min;

    /**
     * @param int|float $min
     */
    public function __construct($min)
    {
        $this->min = $min;
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        return !$this->notGreaterThan($value, self::NOT_GREATER_THAN);
    }

    /**
     * @param mixed $value
     */
    protected function notGreaterThan($value, string $error): bool
    {
        if ($value <= $this->min) {
            $this->addError($error);
            return true;
        }
        return false;
    }

    protected function getMessageParameters(): array
    {
        return array_merge(parent::getMessageParameters(), [
            'min' => $this->min,
        ]);
    }
}
