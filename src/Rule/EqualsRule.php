<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class EqualsRule extends Rule
{
    const NOT_EQUAL = 'Equal::NOT_EQUAL';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_EQUAL => '{{name}} must be equal to "{{testvalue}}"',
    ];

    /** @var mixed */
    protected $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        if ($this->value === $value) {
            return true;
        }
        return $this->addError(self::NOT_EQUAL);
    }

    protected function getMessageParameters(): array
    {
        return array_merge(parent::getMessageParameters(), [
            'testvalue' => $this->value,
        ]);
    }
}
