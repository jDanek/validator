<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class UrlRule extends Rule
{
    const INVALID_URL = 'Url::INVALID_URL';
    const INVALID_SCHEME = 'Url::INVALID_SCHEME';

    /** @var array */
    protected $messageTemplates = [
        self::INVALID_URL => '{{name}} must be a valid URL',
        self::INVALID_SCHEME => '{{name}} must have one of the following schemes: {{schemes}}',
    ];

    /** @var array */
    protected $schemes = [];

    public function __construct(array $schemes = [])
    {
        $this->schemes = $schemes;
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        $url = filter_var($value, FILTER_VALIDATE_URL);

        if ($url !== false) {
            return $this->validateScheme($value);
        }
        return $this->addError(self::INVALID_URL);
    }

    protected function validateScheme(string $value): bool
    {
        if (count($this->schemes) > 0 && !in_array(parse_url($value, PHP_URL_SCHEME), $this->schemes)) {
            return $this->addError(self::INVALID_SCHEME);
        }
        return true;
    }

    protected function getMessageParameters(): array
    {
        return array_merge(parent::getMessageParameters(), [
            'schemes' => implode(', ', $this->schemes),
        ]);
    }
}
