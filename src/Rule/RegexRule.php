<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class RegexRule extends Rule
{
    const NO_MATCH = 'Regex::NO_MATCH';

    /** @var array */
    protected $messageTemplates = [
        self::NO_MATCH => '{{name}} is invalid',
    ];

    /** @var string */
    protected $regex;

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        return $this->match($this->regex, $value, self::NO_MATCH);
    }

    /**
     * @param mixed $value
     */
    protected function match(string $regex, $value, string $reason): bool
    {
        $result = preg_match($regex, $value);

        if ($result === 0) {
            return $this->addError($reason);
        }
        return true;
    }
}
