<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class LengthRule extends Rule
{
    const TOO_SHORT = 'Length::TOO_SHORT';
    const TOO_LONG = 'Length::TOO_LONG';

    /** @var array */
    protected $messageTemplates = [
        self::TOO_SHORT => '{{name}} is too short and must be {{length}} characters long',
        self::TOO_LONG => '{{name}} is too long and must be {{length}} characters long',
    ];

    /** @var int */
    protected $length;

    public function __construct(int $length)
    {
        $this->length = $length;
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        $actualLength = strlen((string)$value);

        if ($actualLength > $this->length) {
            return $this->addError(self::TOO_LONG);
        }
        if ($actualLength < $this->length) {
            return $this->addError(self::TOO_SHORT);
        }
        return true;
    }

    protected function getMessageParameters(): array
    {
        return array_merge(parent::getMessageParameters(), [
            'length' => $this->length,
        ]);
    }
}
