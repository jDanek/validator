<?php

declare(strict_types=1);

namespace Danek\Validator;

class RuleFactory
{
    /** @var array<string, string> */
    private $rulesMap = [];

    public function __construct(array $rulesMap)
    {
        $this->rulesMap = $rulesMap;
    }

    public function createByName(string $ruleName, array $parameters = [])
    {
        if (!isset($this->rulesMap[$ruleName])) {
            throw new \InvalidArgumentException("Rule $ruleName does not exist.");
        }
        return $this->create($this->rulesMap[$ruleName], $parameters);
    }

    /**
     * @throws \InvalidArgumentException if the class does not exist
     * @throws \ReflectionException
     */
    public function create(string $className, array $parameters = [])
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Class $className does not exist.");
        }

        $reflection = new \ReflectionClass($className);
        return $reflection->newInstanceArgs($parameters);
    }
}
