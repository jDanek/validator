<?php

declare(strict_types=1);

namespace Danek\Validator;

class ValidationResult
{
    /** @var bool */
    protected $isValid;

    /** @var array */
    protected $messages;

    /** @var array */
    protected $values;

    /** @var array<Failure> */
    protected $failures;

    public function __construct(bool $isValid, array $failures, array $values)
    {
        $this->isValid = $isValid;
        $this->failures = $failures;
        $this->values = $values;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function isNotValid(): bool
    {
        return !$this->isValid;
    }

    public function getMessages(): array
    {
        if ($this->messages === null) {
            $this->messages = [];
            foreach ($this->failures as $failure) {
                $this->messages[$failure->getKey()][$failure->getReason()] = $failure->format();
            }
        }
        return $this->messages;
    }

    /**
     * @return array<Failure>
     */
    public function getFailures(): array
    {
        return $this->failures;
    }

    public function getValidatedValues(): array
    {
        return $this->values;
    }
}
