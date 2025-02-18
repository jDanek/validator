<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\UuidRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function correctUUIDFormat(): array
    {
        return [
            ['00000000-0000-0000-0000-000000000000'],
            ['05D989B3-A786-E411-80C8-0050568766E4'],
            ['05D989B3-A786-E411-80C8-0050568766E4'],
            ['8672e692-b936-e611-80da-0050568766e4'],
            ['9042c873-ed53-e611-80c6-0050568968d5'],
            ['5c3d167e-6011-11e6-8b77-86f30ca893d3'],
            ['885e561e-6011-11e6-8b77-86f30ca893d3'],
            ['9293b566-6011-11e6-8b77-86f30ca893d3'],
        ];
    }

    public static function correctUUIDv1(): array
    {
        return [
            ['5c3d167e-6011-11e6-8b77-86f30ca893d3'],
            ['885e561e-6011-11e6-8b77-86f30ca893d3'],
            ['9293b566-6011-11e6-8b77-86f30ca893d3'],
        ];
    }

    public static function correctUUIDv2(): array
    {
        return [
            ['5c3d167e-6011-21e6-8b77-86f30ca893d3'],
            ['885e561e-6011-21e6-bb77-86f30ca893d3'],
            ['9293b566-6011-21e6-ab77-86f30ca893d3'],
        ];
    }

    public static function correctUUIDv3(): array
    {
        return [
            ['5C3d167e-6011-31e6-8b77-86f30ca893d3'],
            ['885e561e-6011-31E6-bb77-86f30ca893d3'],
            ['9293b566-6011-31e6-9b77-86f30ca893d3'],
        ];
    }

    public static function correctUUIDNILv4v5(): array
    {
        return array_merge(UuidTest::correctUUIDNIL(), UuidTest::correctUUIDv4(), UuidTest::correctUUIDv5());
    }

    public static function correctUUIDNIL(): array
    {
        return [
            ['00000000-0000-0000-0000-000000000000'],
        ];
    }

    public static function correctUUIDv4(): array
    {
        return [
            ['44c0ffee-988a-49dc-8bad-a55c0de2d1e4'],
            ['de305d54-75b4-431b-adb2-eb6b9e546014'],
            ['00000000-0000-4000-8000-000000000000'],
        ];
    }

    public static function correctUUIDv5(): array
    {
        return [
            ['44c0ffee-988a-59dc-8bad-a55c0de2d1e4'],
            ['de305d54-75b4-531b-adb2-eb6b9e546014'],
            ['00000000-0000-5000-8000-000000000000'],
        ];
    }

    public static function incorrectUUIDv4(): array
    {
        return [
            ['xxc0ffee-988a-49dc-8bad-a55c0de2d1e4'],
            ['123e4567-e89b-12d3-a456-426655440000'],
            ['00000000-0000-0000-0000-000000000000'],      // NIL uuid
            ['a8098c1a-f86e-11da-bd1a-00112444be1e'],      // UUIDv1
            ['6fa459ea-ee8a-3ca4-894e-db77e160355e'],      // UUIDv3
            ['886313e1-3b8a-5372-9b90-0c9aee199e5d'],      // UUIDv5
            ['de305d54-75b4-431b-adb2-eb6b9e546014a'],
            ['fde305d54-75b4-431b-adb2-eb6b9e546014'],
        ];
    }

    public static function correctUUIDv6(): array
    {
        return [
            ['1ec0ffee-988a-69dc-8bad-a55c0de2d1e4'],
            ['1de305d5-75b4-631b-adb2-eb6b9e546014'],
            ['00000000-0000-6000-8000-000000000000'],
        ];
    }

    public static function correctUUIDv7(): array
    {
        return [
            ['017f22e2-79b0-7cc3-98c4-dc0c0c07398f'],
            ['0185e147-4111-7b1e-b962-e66fc6b3f9b6'],
            ['00000000-0000-7000-8000-000000000000'],
        ];
    }

    public static function correctUUIDv8(): array
    {
        return [
            ['00000000-0000-8000-8abc-123456789012'],
            ['aabbccdd-eeff-8111-9223-456789abcdef'],
            ['00000000-0000-8000-8000-000000000000'],
        ];
    }

    public static function incorrectUUIDv6(): array
    {
        return [
            ['xxc0ffee-988a-69dc-8bad-a55c0de2d1e4'],
            ['123e4567-e89b-12d3-a456-426655440000'],
            ['00000000-0000-0000-0000-000000000000'],      // NIL uuid
            ['a8098c1a-f86e-11da-bd1a-00112444be1e'],      // UUIDv1
            ['6fa459ea-ee8a-3ca4-894e-db77e160355e'],      // UUIDv3
            ['44c0ffee-988a-49dc-8bad-a55c0de2d1e4'],      // UUIDv4
            ['886313e1-3b8a-5372-9b90-0c9aee199e5d'],      // UUIDv5
            ['017f22e2-79b0-7cc3-98c4-dc0c0c07398f'],      // UUIDv7
            ['de305d54-75b4-631b-adb2-eb6b9e546014a'],
            ['fde305d54-75b4-631b-adb2-eb6b9e546014'],
        ];
    }

    public static function incorrectUUIDv7(): array
    {
        return [
            ['xxc0ffee-988a-79dc-8bad-a55c0de2d1e4'],
            ['123e4567-e89b-12d3-a456-426655440000'],
            ['00000000-0000-0000-0000-000000000000'],      // NIL uuid
            ['a8098c1a-f86e-11da-bd1a-00112444be1e'],      // UUIDv1
            ['6fa459ea-ee8a-3ca4-894e-db77e160355e'],      // UUIDv3
            ['44c0ffee-988a-49dc-8bad-a55c0de2d1e4'],      // UUIDv4
            ['886313e1-3b8a-5372-9b90-0c9aee199e5d'],      // UUIDv5
            ['1ec0ffee-988a-69dc-8bad-a55c0de2d1e4'],      // UUIDv6
            ['de305d54-75b4-731b-adb2-eb6b9e546014a'],
            ['fde305d54-75b4-731b-adb2-eb6b9e546014'],
        ];
    }

    public static function incorrectUUIDv8(): array
    {
        return [
            ['xxc0ffee-988a-89dc-8bad-a55c0de2d1e4'],
            ['123e4567-e89b-12d3-a456-426655440000'],
            ['00000000-0000-0000-0000-000000000000'],      // NIL uuid
            ['a8098c1a-f86e-11da-bd1a-00112444be1e'],      // UUIDv1
            ['6fa459ea-ee8a-3ca4-894e-db77e160355e'],      // UUIDv3
            ['44c0ffee-988a-49dc-8bad-a55c0de2d1e4'],      // UUIDv4
            ['886313e1-3b8a-5372-9b90-0c9aee199e5d'],      // UUIDv5
            ['1ec0ffee-988a-69dc-8bad-a55c0de2d1e4'],      // UUIDv6
            ['017f22e2-79b0-7cc3-98c4-dc0c0c07398f'],      // UUIDv7
            ['de305d54-75b4-831b-adb2-eb6b9e546014a'],
            ['fde305d54-75b4-831b-adb2-eb6b9e546014'],
        ];
    }

    public static function incorrectVersionsProvider(): array
    {
        return [
            [4],
            [UuidRule::UUID_V8 << 1],
        ];
    }

    /**
     * @dataProvider correctUUIDNIL
     */
    public function testReturnsTrueWhenMatchesUuidNIL($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_NIL);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider correctUUIDv1
     */
    public function testReturnsTrueWhenMatchesUuidV1($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_V1);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider correctUUIDFormat
     */
    public function testReturnsTrueWhenMatchesUuidFormat($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_VALID);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider correctUUIDv2
     */
    public function testReturnsTrueWhenMatchesUuidV2($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_V2);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider correctUUIDv3
     */
    public function testReturnsTrueWhenMatchesUuidV3($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_V3);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider correctUUIDv4
     */
    public function testReturnsTrueWhenMatchesUuidV4($uuid)
    {
        $this->validator->required('guid')->uuid();
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider correctUUIDv5
     */
    public function testReturnsTrueWhenMatchesUuidV5($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_V5);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider correctUUIDv6
     */
    public function testReturnsTrueWhenMatchesUuidV6($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_V6);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider correctUUIDv7
     */
    public function testReturnsTrueWhenMatchesUuidV7($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_V7);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider correctUUIDv8
     */
    public function testReturnsTrueWhenMatchesUuidV8($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_V8);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider correctUUIDNILv4v5
     */
    public function testReturnsTrueWhenMatchingMultipleUuidVersions($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_NIL | UuidRule::UUID_V4 | UuidRule::UUID_V5);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider incorrectUUIDv4
     */
    public function testReturnsFalseOnNoMatch($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_V4);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertFalse($result->isValid());

        $expected = [
            'guid' => [
                UuidRule::INVALID_UUID => 'guid must be a valid UUID (v4)',
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    /**
     * @dataProvider incorrectUUIDv6
     */
    public function testReturnsFalseOnNoMatchV6($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_V6);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertFalse($result->isValid());

        $expected = [
            'guid' => [
                UuidRule::INVALID_UUID => 'guid must be a valid UUID (v6)',
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    /**
     * @dataProvider incorrectUUIDv7
     */
    public function testReturnsFalseOnNoMatchV7($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_V7);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertFalse($result->isValid());

        $expected = [
            'guid' => [
                UuidRule::INVALID_UUID => 'guid must be a valid UUID (v7)',
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    /**
     * @dataProvider incorrectUUIDv8
     */
    public function testReturnsFalseOnNoMatchV8($uuid)
    {
        $this->validator->required('guid')->uuid(UuidRule::UUID_V8);
        $result = $this->validator->validate(['guid' => $uuid]);
        $this->assertFalse($result->isValid());

        $expected = [
            'guid' => [
                UuidRule::INVALID_UUID => 'guid must be a valid UUID (v8)',
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    /**
     * @dataProvider incorrectVersionsProvider
     */
    public function testThrowsExceptionOnUnknownVersion($version)
    {
        $this->expectException(
            \InvalidArgumentException::class,
            'Invalid UUID version mask given. Please choose one of the constants on the Uuid class.'
        );
        $this->validator->required('guid')->uuid(UuidRule::UUID_V8 << 1);
    }

    public function testThrowsExceptionOnNegativeVersion()
    {
        $this->expectException(\InvalidArgumentException::class, 'Invalid UUID version mask given.');

        $this->validator->required('guid')->uuid(-1);
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
