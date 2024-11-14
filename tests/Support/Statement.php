<?php

namespace Danek\Validator\Tests\Support;

class Statement
{
    /** @var string */
    protected $statement;

    /** @var bool */
    protected $result;

    /**
     * @param string $statement
     * @param bool $result
     */
    public function __construct(string $statement, bool $result)
    {
        $this->statement = $statement;
        $this->result = $result;
    }

    public function __invoke(): bool
    {
        return $this->result;
    }

    public function __toString(): string
    {
        return $this->statement;
    }
}
