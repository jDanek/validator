<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class LessThanRule extends Rule
{
    const NOT_LESS_THAN = 'LessThan::NOT_LESS_THAN';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_LESS_THAN => '{{name}} must be less than {{max}}',
    ];

    /** @var int|float */
    protected $max;

    /**
     * @param int|float $max
     */
    public function __construct($max)
    {
        $this->max = $max;
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        return !$this->notLessThan($value, self::NOT_LESS_THAN);
    }

    /**
     * @param mixed $value
     */
    protected function notLessThan($value, string $error): bool
    {
        if ($value >= $this->max) {
            $this->addError($error);
            return true;
        }
        return false;
    }

    protected function getMessageParameters(): array
    {
        return array_merge(parent::getMessageParameters(), [
            'max' => $this->max,
        ]);
    }
}
