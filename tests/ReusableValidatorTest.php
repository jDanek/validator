<?php

namespace Danek\Validator\Tests;

use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class ReusableValidatorTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public function testCanUseNewValidatorEveryTime()
    {
        foreach ($this->getTestData() as $rowKey => $testRow) {
            $this->configureValidator();

            $result = $this->validator->validate($testRow['data']);
            $this->assertEquals($testRow['valid'], $result->isValid());
            $this->assertEquals($testRow['messages'], $result->getMessages());
        }
    }

    private function getTestData(): array
    {
        return [
            [
                'data' => [
                    'customerNr' => 'c000012340',
                    'paymentType' => '',
                    'reference' => '',
                    'description' => 'Test payment 1',
                    'amountPaid' => 1000.00,
                    'notificationDate' => '20150401',
                ],
                'valid' => false,
                'messages' => [
                    'paymentType' => [
                        'NotEmpty::EMPTY_VALUE' => 'paymentType must not be empty',
                    ],
                    'reference' => [
                        'NotEmpty::EMPTY_VALUE' => 'reference must not be empty',
                    ],
                ],
            ],

            [
                'data' => [
                    'customerNr' => 'c000001234',
                    'paymentType' => 'gfh',
                    'description' => 'Test payment 2',
                    'amountPaid' => 25.00,
                    'notificationDate' => '20150401',
                ],
                'valid' => false,
                'messages' => [
                    'paymentType' => [
                        'InArray::NOT_IN_ARRAY' => 'paymentType must be in the defined set of values',
                    ],
                    'reference' => [
                        'Required::NON_EXISTENT_KEY' => 'reference must be provided, but does not exist',
                    ],
                ],
            ],
            [
                'data' => [
                    'customerNr' => 'c000005678',
                    'paymentType' => 'sdf ',
                    'reference' => '',
                    'description' => 'Test payment 3',
                    'amountPaid' => 25.00,
                    'notificationDate' => '20150401',
                ],
                'valid' => false,
                'messages' => [
                    'paymentType' => [
                        'InArray::NOT_IN_ARRAY' => 'paymentType must be in the defined set of values',
                    ],
                    'reference' => [
                        'NotEmpty::EMPTY_VALUE' => 'reference must not be empty',
                    ],
                ],
            ],
        ];
    }

    protected function configureValidator()
    {
        $this->validator = new Validator();

        $this->validator->required('customerNr')->alphaNum();
        $this->validator->required('paymentType')->inArray([
            'downPaymentChangeBankAccount',
            'downPaymentHighSpend',
        ]);
        $this->validator->required('reference')->alphaNum();
        $this->validator->required('description')->lengthBetween(1, 250);
        $this->validator->required('amountPaid')->float()->between(0.01, 10000);
        $this->validator->required('notificationDate')->datetime('Ymd');
    }

    public function testCanReuseValidatorMultipleTimes()
    {
        $this->configureValidator();

        foreach ($this->getTestData() as $rowKey => $testRow) {
            $result = $this->validator->validate($testRow['data']);
            $this->assertEquals($testRow['valid'], $result->isValid());
            $this->assertEquals($testRow['messages'], $result->getMessages());
        }
    }
}
