<?php

declare(strict_types=1);

namespace Danek\Validator\Output;

class Subject
{
    /** @var string */
    protected $key;

    /** @var string */
    protected $name;

    /** @var array<Rule> */
    protected $rules;

    public function __construct(string $key, ?string $name)
    {
        $this->key = $key;
        $this->name = $name;
    }

    /**
     * Adds a rule for this subject.
     */
    public function addRule(Rule $rule): void
    {
        $this->rules[] = $rule;
    }

    /**
     * Returns the key for this subject.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Returns the name for this subject.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return array<Rule>
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
