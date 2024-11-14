<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;
use Danek\Validator\StringifyCallbackTrait;
use Danek\Validator\Value\Container;

class RequiredRule extends Rule
{
    use StringifyCallbackTrait;

    const NON_EXISTENT_KEY = 'Required::NON_EXISTENT_KEY';

    /** @var array */
    protected $messageTemplates = [
        self::NON_EXISTENT_KEY => '{{key}} must be provided, but does not exist',
    ];

    /** @var bool */
    protected $shouldBreak = false;

    /** @var bool */
    protected $required;

    /**
     * Optionally contains a callable to overwrite the required requirement on time of validation.
     *
     * @var callable
     */
    protected $requiredCallback;

    /**
     * Contains the input container.
     *
     * @var Container
     */
    protected $input;

    public function __construct(bool $required)
    {
        $this->required = $required;
    }

    public function shouldBreakChain(): bool
    {
        return $this->shouldBreak;
    }

    public function isValid(string $key, Container $input): bool
    {
        $this->shouldBreak = false;
        $this->required = $this->isRequired($input);

        if (!$input->has($key)) {
            $this->shouldBreak = true;

            if ($this->required) {
                return $this->addError(self::NON_EXISTENT_KEY);
            }
        }

        return $this->validate($input->get($key));
    }

    protected function isRequired(Container $input): bool
    {
        if (isset($this->requiredCallback)) {
            $this->required = call_user_func_array($this->requiredCallback, [$input->getArrayCopy()]);
        }
        return $this->required;
    }

    /**
     * Set a callable to potentially alter the required requirement at the time of validation.
     *
     * This may be incredibly useful for conditional validation.
     *
     * @param callable|bool $required
     */
    public function setRequired($required): self
    {
        if (is_callable($required)) {
            return $this->setRequiredCallback($required);
        }

        return $this->overwriteRequired((bool)$required);
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        return true;
    }

    protected function setRequiredCallback(callable $requiredCallback): self
    {
        $this->requiredCallback = $requiredCallback;
        return $this;
    }

    protected function overwriteRequired(bool $required): self
    {
        $this->required = $required;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getMessageParameters(): array
    {
        return array_merge(parent::getMessageParameters(), [
            'required' => $this->required,
            'callback' => $this->getCallbackAsString($this->requiredCallback),
        ]);
    }
}
