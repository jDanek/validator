<?php

declare(strict_types=1);

namespace Danek\Validator;

use Danek\Validator\Output\Subject;
use Danek\Validator\Value\Container;

abstract class Rule
{
    /**
     * Contains an array of all values to be validated.
     *
     * @var array
     */
    protected $values;

    /**
     * Contains an array of messages to be returned on validation errors.
     *
     * @var array
     */
    protected $messageTemplates = [];

    /**
     * Contains a reference to the MessageStack to append errors to.
     *
     * @var MessageStack
     */
    protected $messageStack;

    /**
     * The key we have to validate the value of.
     *
     * @var string
     */
    protected $key;

    /**
     * The name may be used in validation error messages.
     *
     * @var string
     */
    protected $name;

    /**
     * This indicates whether or not the rule can and should break the chain it's in.
     */
    public function shouldBreakChain(): bool
    {
        return false;
    }

    /**
     * This indicates whether or not the rule should break the chain it's in on validation failure.
     */
    public function shouldBreakChainOnError(): bool
    {
        return false;
    }

    /**
     * Registers the message stack to append errors to.
     */
    public function setMessageStack(MessageStack $messageStack): self
    {
        $this->messageStack = $messageStack;
        return $this;
    }

    /**
     * Determines whether or not the value of $key is valid in the array $values and returns the result as a bool.
     */
    public function isValid(string $key, Container $input): bool
    {
        return $this->validate($input->get($key));
    }

    /**
     * @param mixed $value
     */
    abstract public function validate($value): bool;

    /**
     * Attach a representation of this rule to the Output\Subject $subject.
     *
     * @internal
     */
    public function output(Subject $subject, MessageStack $messageStack): void
    {
        $this->setParameters($subject->getKey(), $subject->getName());

        $outputRule = new Output\Rule(
            $this->getShortName(),
            $this->getMessageTemplates($messageStack),
            $this->getMessageParameters()
        );

        $subject->addRule($outputRule);
    }

    /**
     * Sets the default parameters for each validation rule (key and name).
     */
    public function setParameters(string $key, ?string $name): self
    {
        $this->key = $key;
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of this class, without the namespace.
     */
    protected function getShortName(): string
    {
        return substr(get_class($this), strrpos(get_class($this), '\\') + 1);
    }

    /**
     * Get an array of Message Templates to be returned in output.
     */
    protected function getMessageTemplates(MessageStack $messageStack): array
    {
        $messages = $this->messageTemplates;
        foreach ($messages as $reason => $message) {
            $overwrite = $messageStack->getOverwrite($reason, $this->key);

            if (is_string($overwrite)) {
                $messages[$reason] = $overwrite;
            }
        }

        return $messages;
    }

    /**
     * Return an array of all parameters that might be replaced in the validation error messages.
     */
    protected function getMessageParameters(): array
    {
        $name = $this->name ?? str_replace('_', ' ', $this->key);

        return [
            'key' => $this->key,
            'name' => $name,
        ];
    }

    /**
     * Appends the error for reason $reason to the MessageStack.
     */
    protected function addError(string $reason): bool
    {
        $this->messageStack->append(
            new Failure(
                $this->key,
                $reason,
                $this->getMessage($reason),
                $this->getMessageParameters()
            )
        );

        return false;
    }

    /**
     * Returns an error message for the reason $reason, or an empty string if it doesn't exist.
     */
    protected function getMessage(string $reason): string
    {
        $messageTemplate = '';
        if (array_key_exists($reason, $this->messageTemplates)) {
            $messageTemplate = $this->messageTemplates[$reason];
        }

        return $messageTemplate;
    }
}
