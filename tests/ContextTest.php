<?php

namespace Danek\Validator\Tests;

use Danek\Validator\Chain;
use Danek\Validator\Rule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public function testCanBeValidatedIndependently()
    {
        $this->validator->context('insert', function (Validator $context) {
            $context->required('first_name')->length(5);
        });

        $this->validator->required('first_name')->length(3);

        $result = $this->validator->validate(['first_name' => 'berry'], 'insert');

        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    public function testCanHaveIndependentMessages()
    {
        $this->validator->context('insert', function (Validator $context) {
            $context->required('first_name')->length(5);

            $context->overwriteMessages([
                'first_name' => [
                    Rule\LengthRule::TOO_SHORT => 'This is from inside the context.',
                ],
            ]);
        });

        $this->validator->overwriteMessages([
            'first_name' => [
                Rule\LengthRule::TOO_SHORT => 'This is outside of the context',
            ],
        ]);

        $result = $this->validator->validate(['first_name' => 'Rick'], 'insert');

        $expected = [
            'first_name' => [
                Rule\LengthRule::TOO_SHORT => 'This is from inside the context.',
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    public function testMessagesWillBeInheritedFromDefaultContext()
    {
        $this->validator->context('insert', function (Validator $context) {
            $context->required('first_name')->length(5);
        });

        $this->validator->overwriteMessages([
            'first_name' => [
                Rule\LengthRule::TOO_SHORT => 'This is outside of the context',
            ],
        ]);

        $result = $this->validator->validate(['first_name' => 'Rick'], 'insert');

        $expected = [
            'first_name' => [
                Rule\LengthRule::TOO_SHORT => 'This is outside of the context',
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    public function testContextCanCopyRulesFromOtherContext()
    {
        $this->validator->context('insert', function (Validator $context) {
            $context->overwriteMessages([
                'first_name' => [
                    Rule\LengthRule::TOO_SHORT => 'From inside the "insert" context.',
                ],
            ]);

            $context->required('first_name')->length(5);
        });

        $this->validator->context('update', function (Validator $context) {
            $context->copyContext('insert');
        });

        $result = $this->validator->validate(['first_name' => 'Rick'], 'update');
        $this->assertFalse($result->isValid());

        $expected = [
            'first_name' => [
                Rule\LengthRule::TOO_SHORT => 'From inside the "insert" context.',
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    public function testContextCopyCanAlterChains()
    {
        $this->validator->context('insert', function (Validator $context) {
            $context->required('first_name')->length(5);
        });

        $this->validator->context('update', function (Validator $context) {
            $context->copyContext('insert', function (array $chains) {
                /** @var Chain $chain */
                foreach ($chains as $chain) {
                    $chain->required(function () {
                        return false; // all fields optional.
                    });
                }
            });
        });

        $result = $this->validator->validate([], 'update');
        $this->assertTrue($result->isValid());
    }

    public function testContextCopyClonesButDoesNotOverwrite()
    {
        $this->validator->context('insert', function (Validator $context) {
            $context->required('first_name')->length(5);
        });

        $this->validator->context('update', function (Validator $context) {
            $context->copyContext('insert', function (array $chains) {
                /** @var Chain $chain */
                foreach ($chains as $chain) {
                    $chain->required(function () {
                        return false; // all fields optional.
                    });
                }
            });
        });

        $result = $this->validator->validate([], 'insert');
        $this->assertFalse($result->isValid());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
