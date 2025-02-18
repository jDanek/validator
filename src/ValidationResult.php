<?php

declare(strict_types=1);

namespace Danek\Validator;

class ValidationResult
{
    public const NESTING_KEY_REASON = 'key.reason';
    public const NESTING_REASON_KEY = 'reason.key';
    public const NESTING_KEY = 'key';
    public const NESTING_REASON = 'reason';
    public const NESTING_NONE = null;

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

    /**
     * @param string|null $nesting see NESTING_* constants in ValidationResult
     */
    public function getMessages(?string $nesting = self::NESTING_KEY_REASON): array
    {
        if ($this->messages === null) {
            $this->messages = [];

            foreach ($this->failures as $failure) {
                $formattedMessage = $failure->format();

                switch ($nesting) {
                    case self::NESTING_KEY:
                        $this->messages[$failure->getKey()] = $formattedMessage;
                        break;
                    case self::NESTING_REASON:
                        $this->messages[$failure->getReason()] = $formattedMessage;
                        break;
                    case self::NESTING_KEY_REASON:
                        $this->messages[$failure->getKey()][$failure->getReason()] = $formattedMessage;
                        break;
                    case self::NESTING_REASON_KEY:
                        $this->messages[$failure->getReason()][$failure->getKey()] = $formattedMessage;
                        break;
                    case self::NESTING_NONE:
                    default: // null or invalid input, default behavior
                        $this->messages[] = $formattedMessage;
                        break;
                }
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
