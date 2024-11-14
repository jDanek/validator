<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

class AlphaNumRule extends RegexRule
{
    const NOT_ALPHA_NUM = 'AlphaNum::NOT_ALPHA_NUM';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_ALPHA_NUM => '{{name}} may only consist out of numeric and alphabetic characters',
    ];

    public function __construct(bool $allowSpaces = false)
    {
        parent::__construct($allowSpaces ? '~^[\p{L}0-9\s]*$~iu' : '~^[\p{L}0-9]*$~iu');
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        return $this->match($this->regex, $value, self::NOT_ALPHA_NUM);
    }
}
