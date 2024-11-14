<?php

declare(strict_types=1);

namespace Danek\Validator\Output;

class Rule
{
    /** @var string */
    protected $name;

    /** @var array */
    protected $messages;

    /** @var array */
    protected $parameters;

    public function __construct(string $name, array $messages, array $parameters)
    {
        $this->name = $name;
        $this->messages = $messages;
        $this->parameters = $parameters;
    }

    /**
     * Returns the name (short class name) for this rule.
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
