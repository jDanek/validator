<?php
namespace Danek\Validator\Tests;

use Danek\Validator\Chain;
use Danek\Validator\MappedValidator;
use PHPUnit\Framework\TestCase;

class MappedValidatorTest extends TestCase
{
    public function testSetRulesWithArrayRules()
    {
        $validator = new MappedValidator();
        $rules = [
            'name' => [
                'required' => true,
                'string' => true,
                'length_between' => [2, 255],
            ],
        ];

        $result = $validator->setRules($rules);
        $this->assertInstanceOf(MappedValidator::class, $result);
    }

    public function testSetRulesWithStringRules()
    {
        $validator = new MappedValidator();
        $rules = [
            'name' => 'required|string|length_between:2,255',
        ];

        $result = $validator->setRules($rules);
        $this->assertInstanceOf(MappedValidator::class, $result);
    }

    public function testApplyArrayRulesToField()
    {
        $validator = $this->getMockBuilder(MappedValidator::class)
            ->onlyMethods(['getChain'])
            ->getMock();

        $chain = $this->createMock(Chain::class);
        $chain->expects($this->once())
            ->method('string')
            ->willReturnSelf();
        $chain->expects($this->once())
            ->method('lengthBetween')
            ->with(2, 255)
            ->willReturnSelf();

        $validator->method('getChain')
            ->willReturn($chain);

        $validator->setRules([
            'name' => [
                'string' => true,
                'length_between' => [2, 255],
            ],
        ]);
    }

    public function testApplyStringRulesToField()
    {
        $validator = $this->getMockBuilder(MappedValidator::class)
            ->onlyMethods(['getChain'])
            ->getMock();

        $chain = $this->createMock(Chain::class);
        $chain->expects($this->once())
            ->method('string')
            ->willReturnSelf();
        $chain->expects($this->once())
            ->method('lengthBetween')
            ->with(2, 255)
            ->willReturnSelf();

        $validator->method('getChain')
            ->willReturn($chain);

        $validator->setRules([
            'name' => 'string|length_between:2,255',
        ]);
    }
}
