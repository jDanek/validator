<?php

declare(strict_types=1);

namespace Danek\Validator;

class Failure
{
    /** @var string */
    protected $key;

    /** @var string */
    protected $reason;

    /** @var string */
    protected $template;

    /** @var array */
    protected $parameters = [];

    public function __construct(string $key, string $reason, string $template, array $parameters)
    {
        $this->key = $key;
        $this->reason = $reason;
        $this->template = $template;
        $this->parameters = $parameters;
    }

    public function format(): string
    {
        $replace = function ($matches) {
            if (array_key_exists($matches[1], $this->parameters)) {
                return $this->parameters[$matches[1]];
            }
            return $matches[0];
        };

        return preg_replace_callback('~{{([^}\s]+)}}~', $replace, $this->template);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function overwriteMessageTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function overwriteKey(string $key): void
    {
        $this->key = $key;
    }
}
