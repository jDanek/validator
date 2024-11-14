<?php

namespace Danek\Validator\Tests;

use Danek\Validator\Failure;
use Danek\Validator\MessageStack;
use Danek\Validator\Rule\NotEmptyRule;
use Danek\Validator\Rule\RequiredRule;
use PHPUnit\Framework\TestCase;

class MessageStackTest extends TestCase
{
    public function testMergeWillMergeMessagesOfOtherMessageStacks()
    {
        $stack = new MessageStack();
        $stackTwo = new MessageStack();

        $stack->overwriteMessages([
            'foo' => [
                RequiredRule::NON_EXISTENT_KEY => 'Non existent key',
            ],
        ]);

        $stack->overwriteDefaultMessages([
            NotEmptyRule::EMPTY_VALUE => 'Empty value',
        ]);

        $stackTwo->merge($stack);

        $messages = [
            $stackTwo->getOverwrite(RequiredRule::NON_EXISTENT_KEY, 'foo'),
            $stackTwo->getOverwrite(NotEmptyRule::EMPTY_VALUE, 'bar'),
        ];

        $expected = [
            'Non existent key',
            'Empty value',
        ];

        $this->assertEquals($expected, $messages);
    }

    public function testOverwritesDefaultMessage()
    {
        $stack = new MessageStack();

        $stack->overwriteDefaultMessages([
            NotEmptyRule::EMPTY_VALUE => 'Empty value',
        ]);

        $stack->append(new Failure('foo', NotEmptyRule::EMPTY_VALUE, 'Not important', []));

        $expected = [
            new Failure('foo', NotEmptyRule::EMPTY_VALUE, 'Empty value', []),
        ];

        $this->assertEquals($expected, $stack->getFailures());
    }

    public function testOverwritesSpecificMessage()
    {
        $stack = new MessageStack();

        $stack->overwriteMessages([
            'foo' => [
                NotEmptyRule::EMPTY_VALUE => 'Empty value',
            ],
        ]);

        $stack->append(new Failure('foo', NotEmptyRule::EMPTY_VALUE, 'Not important', []));

        $expected = [
            new Failure('foo', NotEmptyRule::EMPTY_VALUE, 'Empty value', []),
        ];

        $this->assertEquals($expected, $stack->getFailures());
    }
}
