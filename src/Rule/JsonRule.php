<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class JsonRule extends Rule
{
    const INVALID_FORMAT = 'Json::INVALID_VALUE';

    /** @var array */
    protected $messageTemplates = [
        self::INVALID_FORMAT => '{{name}} must be a valid JSON string',
    ];

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        if (!is_string($value)) {
            return $this->addError(self::INVALID_FORMAT);
        }

        json_decode($value);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->addError(self::INVALID_FORMAT);
        }

        return true;
    }
}
