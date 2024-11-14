<?php

declare(strict_types=1);

namespace Danek\Validator;

/**
 * This Trait is for rendering callable objects to a string, if that's possible.
 */
trait StringifyCallbackTrait
{
    /**
     * Returns a string representation of a callback, if it implements the __toString method.
     */
    protected function getCallbackAsString(?callable $callback): string
    {
        if (is_object($callback) && method_exists($callback, '__toString')) {
            return (string)$callback;
        }
        return '';
    }
}
