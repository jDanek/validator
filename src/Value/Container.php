<?php

declare(strict_types=1);

namespace Danek\Validator\Value;

class Container
{
    /**
     * Contains the values (either input or output).
     *
     * @var array
     */
    protected $values = [];

    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * Determines whether or not the container has a value for key $key.
     */
    public function has(string $key)
    {
        return $this->traverse($key, false);
    }

    /**
     * Traverses the key using dot notation. Based on the second parameter, it will return the value or if it was set.
     *
     * @return mixed
     */
    protected function traverse(string $key, bool $returnValue = true)
    {
        $value = $this->values;
        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return false;
            }
            $value = $value[$part];
        }
        return $returnValue ? $value : true;
    }

    /**
     * Returns the value for the key $key, or null if the value doesn't exist.
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->traverse($key, true);
    }

    /**
     * Set the value of $key to $value.
     *
     * @param mixed $value
     */
    public function set(string $key, $value): self
    {
        if (strpos($key, '.') !== false) {
            return $this->setTraverse($key, $value);
        }
        $this->values[$key] = $value;
        return $this;
    }

    /**
     * Uses dot-notation to set a value.
     *
     * @param mixed $value
     */
    protected function setTraverse(string $key, $value): self
    {
        $parts = explode('.', $key);
        $ref = &$this->values;

        foreach ($parts as $i => $part) {
            if ($i < count($parts) - 1 && (!isset($ref[$part]) || !is_array($ref[$part]))) {
                $ref[$part] = [];
            }
            $ref = &$ref[$part];
        }

        $ref = $value;
        return $this;
    }

    /**
     * Returns a plain array representation of the Value\Container object.
     */
    public function getArrayCopy(): array
    {
        return $this->values;
    }
}
