<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;
use Exception;

class HashRule extends Rule
{
    const INVALID_FORMAT = 'Hash::INVALID_FORMAT';

    const ALGO_MD5 = 'md5';
    const ALGO_SHA1 = 'sha1';
    const ALGO_SHA256 = 'sha256';
    const ALGO_SHA512 = 'sha512';
    const ALGO_CRC32 = 'crc32';

    /** @var array */
    protected $messageTemplates = [
        self::INVALID_FORMAT => '{{name}} must be a valid hash',
    ];

    /** @var string */
    protected $hashAlgorithm;

    /** @var bool */
    protected $allowUppercase;

    public function __construct(string $hashAlgorithm, bool $allowUppercase = false)
    {
        $this->hashAlgorithm = $hashAlgorithm;
        $this->allowUppercase = $allowUppercase;

        $this->messageTemplates = [
            self::INVALID_FORMAT => sprintf('{{name}} must be a valid %s hash', $hashAlgorithm),
        ];
    }

    /**
     * Validates if the value is a valid cryptographic hash.
     *
     * @param mixed $value
     * @throws Exception
     */
    public function validate($value): bool
    {
        $algorithmsLengths = [
            self::ALGO_MD5 => 32,
            self::ALGO_SHA1 => 40,
            self::ALGO_SHA256 => 64,
            self::ALGO_SHA512 => 128,
            self::ALGO_CRC32 => 8,
        ];

        if (!isset($algorithmsLengths[$this->hashAlgorithm])) {
            throw new Exception('an invalid hashAlgorithm has been provided.');
        }

        if ($this->validateHexString($value, $algorithmsLengths[$this->hashAlgorithm])) {
            return true;
        }

        return $this->addError(self::INVALID_FORMAT);
    }

    private function validateHexString(string $value, int $length): bool
    {
        $caseSensitive = $this->allowUppercase ? 'i' : '';

        return preg_match(sprintf('/^[0-9a-f]{%s}$/%s', $length, $caseSensitive), $value) === 1;
    }
}
