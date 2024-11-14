<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;
use Danek\Validator\ValidationResult;
use Danek\Validator\Validator;

class EachRule extends Rule
{
    const NOT_AN_ARRAY = 'Each::NOT_AN_ARRAY';
    const NOT_AN_ARRAY_ITEM = 'Each::NOT_AN_ARRAY_ITEM';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_AN_ARRAY => '{{name}} must be an array',
        self::NOT_AN_ARRAY_ITEM => 'Each {{name}} item must be an array',
    ];

    /** @var callable */
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Validates if $value is array, validate each inner array of $value, and return result.
     *
     * @param mixed $value
     */
    public function validate($value): bool
    {
        if (!is_array($value)) {
            return $this->addError(self::NOT_AN_ARRAY);
        }

        $result = true;
        foreach ($value as $index => $innerValue) {
            if (!is_array($innerValue)) {
                return $this->addError(self::NOT_AN_ARRAY_ITEM);
            }

            $result = $this->validateValue($index, $innerValue) && $result;
        }
        return $result;
    }

    /**
     * @param mixed $index
     * @param mixed $value
     */
    protected function validateValue($index, $value): bool
    {
        $innerValidator = new Validator();

        call_user_func($this->callback, $innerValidator);

        $result = $innerValidator->validate($value);

        if (!$result->isValid()) {
            $this->handleError($index, $result);
            return false;
        }

        return true;
    }

    /**
     * @param mixed $index
     */
    protected function handleError($index, ValidationResult $result): void
    {
        foreach ($result->getFailures() as $failure) {
            $failure->overwriteKey(
                sprintf('%s.%s.%s', $this->key, $index, $failure->getKey())
            );

            $this->messageStack->append($failure);
        }
    }
}
