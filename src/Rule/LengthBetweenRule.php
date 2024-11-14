<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

class LengthBetweenRule extends BetweenRule
{
    const TOO_LONG = 'LengthBetween::TOO_LONG';
    const TOO_SHORT = 'LengthBetween::TOO_SHORT';

    /** @var array */
    protected $messageTemplates = [
        self::TOO_LONG => '{{name}} must be {{max}} characters or shorter',
        self::TOO_SHORT => '{{name}} must be {{min}} characters or longer',
    ];

    /**
     * @param int $min
     * @param int|null $max
     */
    public function __construct(int $min, ?int $max = null)
    {
        parent::__construct($min, $max);
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        $length = strlen($value);

        return !$this->tooSmall($length, self::TOO_SHORT) && !$this->tooLarge($length, self::TOO_LONG);
    }

    /**
     * @param mixed $value
     */
    protected function tooLarge($value, string $error): bool
    {
        if ($this->max !== null) {
            return parent::tooLarge($value, $error);
        }
        return false;
    }

    /**
     * Returns the parameters that may be used in a validation message.
     */
    protected function getMessageParameters(): array
    {
        return array_merge(parent::getMessageParameters(), [
            'min' => $this->min,
            'max' => $this->max,
        ]);
    }
}
