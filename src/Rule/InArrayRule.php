<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class InArrayRule extends Rule
{
    const NOT_IN_ARRAY = 'InArray::NOT_IN_ARRAY';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_IN_ARRAY => '{{name}} must be in the defined set of values',
    ];

    /** @var array */
    protected $array = [];

    /** @var bool */
    protected $strict;

    public function __construct(array $array, bool $strict = true)
    {
        $this->array = $array;
        $this->strict = $strict;
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        if (in_array($value, $this->array, $this->strict)) {
            return true;
        }
        return $this->addError(self::NOT_IN_ARRAY);
    }

    protected function getMessageParameters(): array
    {
        $quote = function ($value) {
            return '"' . $value . '"';
        };

        return array_merge(parent::getMessageParameters(), [
            'values' => implode(', ', array_map($quote, $this->array)),
        ]);
    }
}
