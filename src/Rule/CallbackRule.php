<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Exception\InvalidValueException;
use Danek\Validator\Rule;
use Danek\Validator\StringifyCallbackTrait;
use Danek\Validator\Value\Container;

class CallbackRule extends Rule
{
    use StringifyCallbackTrait;

    const INVALID_VALUE = 'Callback::INVALID_VALUE';

    /** @var array */
    protected $messageTemplates = [
        self::INVALID_VALUE => '{{name}} is invalid',
    ];

    /** @var callable */
    protected $callback;

    /** @var Container */
    protected $input;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        try {
            $result = call_user_func($this->callback, $value, $this->values);

            if ($result === true) {
                return true;
            }
            return $this->addError(self::INVALID_VALUE);
        } catch (InvalidValueException $exception) {
            $reason = $exception->getIdentifier();
            $this->messageTemplates[$reason] = $exception->getMessage();

            return $this->addError($reason);
        }
    }

    public function isValid(string $key, Container $input): bool
    {
        $this->values = $input->getArrayCopy();

        return parent::isValid($key, $input);
    }

    /**
     * {@inheritdoc}
     */
    protected function getMessageParameters(): array
    {
        return array_merge(parent::getMessageParameters(), [
            'callback' => $this->getCallbackAsString($this->callback),
        ]);
    }
}
