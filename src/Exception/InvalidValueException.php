<?php

declare(strict_types=1);

namespace Danek\Validator\Exception;

use Danek\Validator\ExceptionInterface;
use Exception;

class InvalidValueException extends Exception implements ExceptionInterface
{
    /** @var string */
    protected $identifier;

    /** @var string */
    protected $message;

    /**
     * @param string $message
     * @param string $identifier
     * @param Exception|null $previous
     */
    public function __construct($message, $identifier, Exception $previous = null)
    {
        $this->message = $message;
        $this->identifier = $identifier;

        parent::__construct($message, 0, $previous);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
