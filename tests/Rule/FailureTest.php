<?php

namespace Danek\Validator\Tests;

use Danek\Validator\Failure;
use PHPUnit\Framework\TestCase;

class FailureTest extends TestCase
{
    public function testFailureCanReplacePlaceholders()
    {
        $failure = new Failure('key', 'reason', 'The value of "key" is "{{key}}"', ['key' => 'foo']);

        $this->assertEquals('The value of "key" is "foo"', $failure->format());
    }

    public function testIgnoresWhitespaceInPlaceholders()
    {
        $failure = new Failure('key', 'reason', 'The value of "key" is "{{key}}"', ['key' => 'foo']);

        $this->assertEquals('The value of "key" is "foo"', $failure->format());
    }

    public function testWillNotReplaceUnknownPlaceholders()
    {
        $failure = new Failure('key', 'reason', 'The value of "key" is "{{key}}"', []);

        $this->assertEquals('The value of "key" is "{{key}}"', $failure->format());
    }
}
