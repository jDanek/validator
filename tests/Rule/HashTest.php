<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\HashRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class HashTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    /**
     * Returns a list of hashes considered valid.
     */
    public static function getValidHashes(): array
    {
        return [
            [hash('md5', ''), HashRule::ALGO_MD5, false],
            [strtoupper(hash('md5', '')), HashRule::ALGO_MD5, true],
            [hash('sha1', ''), HashRule::ALGO_SHA1, false],
            [hash('sha256', ''), HashRule::ALGO_SHA256, false],
            [hash('sha512', ''), HashRule::ALGO_SHA512, false],
            [hash('crc32', ''), HashRule::ALGO_CRC32, false],
        ];
    }

    /**
     * Returns a list of hashes considered invalid.
     */
    public static function getInvalidHashes(): array
    {
        return [
            [hash('sha512', ''), HashRule::ALGO_MD5, false],
            [strtoupper(hash('md5', '')), HashRule::ALGO_MD5, false],
            [hash('md5', ''), HashRule::ALGO_SHA1, false],
            [hash('crc32', ''), HashRule::ALGO_SHA256, false],
            [hash('sha1', ''), HashRule::ALGO_SHA512, false],
            [hash('sha256', ''), HashRule::ALGO_CRC32, false],
        ];
    }

    public function setUp(): void
    {
        $this->validator = new Validator();
    }

    /**
     * @dataProvider getValidHashes
     * @param string $value
     */
    public function testReturnsTrueOnValidHashes($value, string $hashAlgorithm, bool $allowUppercase)
    {
        $this->validator->required('hash')->hash($hashAlgorithm, $allowUppercase);
        $result = $this->validator->validate(['hash' => $value]);
        $this->assertTrue($result->isValid());
    }

    /**
     * @dataProvider getInvalidHashes
     * @param string $value
     * @param string $hashAlgorithm
     */
    public function testReturnsFalseOnInvalidHashes($value, $hashAlgorithm, bool $allowUppercase)
    {
        $this->validator->required('hash')->hash($hashAlgorithm, $allowUppercase);
        $result = $this->validator->validate(['hash' => $value]);
        $this->assertFalse($result->isValid());
        $expected = [
            'hash' => [
                HashRule::INVALID_FORMAT => sprintf('hash must be a valid %s hash', $hashAlgorithm),
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }
}
