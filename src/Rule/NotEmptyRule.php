<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;
use Danek\Validator\StringifyCallbackTrait;
use Danek\Validator\Value\Container;

class NotEmptyRule extends Rule
{
    use StringifyCallbackTrait;

    const EMPTY_VALUE = 'NotEmpty::EMPTY_VALUE';

    /** @var array */
    protected $messageTemplates = [
        self::EMPTY_VALUE => '{{name}} must not be empty',
    ];

    /** @var bool */
    protected $shouldBreak = false;

    /** @var bool */
    protected $allowEmpty;

    /**
     * Optionally contains a callable to overwrite the allow empty requirement on time of validation.
     *
     * @var callable
     */
    protected $allowEmptyCallback;

    /** @var Container */
    protected $input;

    public function __construct(bool $allowEmpty)
    {
        $this->allowEmpty = (bool)$allowEmpty;
    }

    public function shouldBreakChain(): bool
    {
        return $this->shouldBreak;
    }

    /**
     * @inheritdoc
     */
    public function isValid(string $key, Container $input): bool
    {
        $this->input = $input;

        return $this->validate($input->get($key));
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        $this->shouldBreak = false;
        if ($this->isEmpty($value)) {
            $this->shouldBreak = true;

            return $this->allowEmpty($this->input) || $this->addError(self::EMPTY_VALUE);
        }
        return true;
    }

    /**
     * @param mixed $value
     */
    protected function isEmpty($value): bool
    {
        if (is_string($value) && strlen($value) === 0) {
            return true;
        } elseif ($value === null) {
            return true;
        } elseif (is_array($value) && count($value) === 0) {
            return true;
        }

        return false;
    }

    protected function allowEmpty(Container $input): bool
    {
        if (isset($this->allowEmptyCallback)) {
            $this->allowEmpty = call_user_func($this->allowEmptyCallback, $input->getArrayCopy());
        }
        return $this->allowEmpty;
    }

    /**
     * Set a callable or boolean value to potentially alter the allow empty requirement at the time of validation.
     *
     * This may be incredibly useful for conditional validation.
     *
     * @param callable|bool $allowEmpty
     */
    public function setAllowEmpty($allowEmpty): self
    {
        if (is_callable($allowEmpty)) {
            return $this->setAllowEmptyCallback($allowEmpty);
        }
        return $this->overwriteAllowEmpty($allowEmpty);
    }

    protected function setAllowEmptyCallback(callable $allowEmptyCallback): self
    {
        $this->allowEmptyCallback = $allowEmptyCallback;
        return $this;
    }

    protected function overwriteAllowEmpty(bool $allowEmpty): self
    {
        $this->allowEmpty = $allowEmpty;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getMessageParameters(): array
    {
        return array_merge(parent::getMessageParameters(), [
            'allowEmpty' => $this->allowEmpty,
            'callback' => $this->getCallbackAsString($this->allowEmptyCallback),
        ]);
    }
}
