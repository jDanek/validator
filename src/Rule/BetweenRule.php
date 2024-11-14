<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class BetweenRule extends Rule
{
    const TOO_BIG = 'Between::TOO_BIG';
    const TOO_SMALL = 'Between::TOO_SMALL';

    /** @var array */
    protected $messageTemplates = [
        self::TOO_BIG => '{{name}} must be less than or equal to {{max}}',
        self::TOO_SMALL => '{{name}} must be greater than or equal to {{min}}',
    ];

    /** @var int|float */
    protected $min;

    /** @var int|float|null */
    protected $max;

    /**
     * @param int|float $min
     * @param int|float|null $max
     */
    public function __construct($min, $max = null)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        return !$this->tooSmall($value, self::TOO_SMALL) && !$this->tooLarge($value, self::TOO_BIG);
    }

    /**
     * @param mixed $value
     */
    protected function tooSmall($value, string $error): bool
    {
        if ($value < $this->min) {
            $this->addError($error);
            return true;
        }
        return false;
    }

    /**
     * @param mixed $value
     */
    protected function tooLarge($value, string $error): bool
    {
        if ($this->max === null) {
            return false;
        }
        if ($value > $this->max) {
            $this->addError($error);
            return true;
        }
        return false;
    }

    protected function getMessageParameters(): array
    {
        return array_merge(parent::getMessageParameters(), [
            'min' => $this->min,
            'max' => $this->max,
        ]);
    }
}
