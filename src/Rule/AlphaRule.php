<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

class AlphaRule extends RegexRule
{
    const NOT_ALPHA = 'Alpha::NOT_ALPHA';

    /** @var array */
    protected $messageTemplates = [
        self::NOT_ALPHA => '{{name}} may only consist out of alphabetic characters',
    ];

    public function __construct(bool $allowWhitespace = false)
    {
        parent::__construct($allowWhitespace ? '~^[\p{L}\s]*$~iu' : '~^[\p{L}]*$~ui');
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        return $this->match($this->regex, $value, self::NOT_ALPHA);
    }
}
