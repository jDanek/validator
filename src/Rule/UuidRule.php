<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use InvalidArgumentException;

class UuidRule extends RegexRule
{
    const INVALID_UUID = 'Uuid::INVALID_UUID';

    /**
     * UUID NIL & version binary masks
     */
    const UUID_VALID = 0b00000100;
    const UUID_NIL = 0b00000001;
    const UUID_V1 = 0b00000010;
    const UUID_V2 = 0b00001000;
    const UUID_V3 = 0b00010000;
    const UUID_V4 = 0b00100000;
    const UUID_V5 = 0b01000000;
    const UUID_V6 = 0b10000000;
    const UUID_V7 = 0b000100000000;
    const UUID_V8 = 0b001000000000;


    /**
     * An array of all validation regexes.
     *
     * @var array
     */
    protected $regexes = [
        self::UUID_VALID => '~^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$~i',
        self::UUID_NIL => '~^[0]{8}-[0]{4}-[0]{4}-[0]{4}-[0]{12}$~i',
        self::UUID_V1 => '~^[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
        self::UUID_V2 => '~^[0-9a-f]{8}-[0-9a-f]{4}-2[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
        self::UUID_V3 => '~^[0-9a-f]{8}-[0-9a-f]{4}-3[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
        self::UUID_V4 => '~^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
        self::UUID_V5 => '~^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
        self::UUID_V6 => '~^[0-9a-f]{8}-[0-9a-f]{4}-6[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
        self::UUID_V7 => '~^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
        self::UUID_V8 => '~^[0-9a-f]{8}-[0-9a-f]{4}-8[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
    ];

    /**
     * An array of names for all the versions
     *
     * @var array
     */
    protected $versionNames = [
        self::UUID_VALID => 'valid format',
        self::UUID_NIL => 'NIL',
        self::UUID_V1 => 'v1',
        self::UUID_V2 => 'v2',
        self::UUID_V3 => 'v3',
        self::UUID_V4 => 'v4',
        self::UUID_V5 => 'v5',
        self::UUID_V6 => 'v6',
        self::UUID_V7 => 'v7',
        self::UUID_V8 => 'v8',
    ];

    /**
     * The message templates which can be returned by this validator.
     *
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID_UUID => '{{name}} must be a valid UUID ({{version}})',
    ];

    /**
     * The version of the UUID you'd like to check.
     *
     * @var int
     */
    protected $version;

    public function __construct(int $version = self::UUID_VALID)
    {
        if ($version > self::UUID_V8 || $version < 0) {
            throw new InvalidArgumentException(
                'Invalid UUID version mask given. Please choose one of the constants on the Uuid class.'
            );
        }
        $this->version = $version;
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        foreach ($this->regexes as $version => $regex) {
            if (($version & $this->version) === $version && preg_match($regex, $value) > 0) {
                return true;
            }
        }
        return $this->addError(self::INVALID_UUID);
    }

    protected function getMessageParameters(): array
    {
        $versions = [];
        foreach (array_keys($this->regexes) as $version) {
            if (($version & $this->version) === $version) {
                $versions[] = $this->versionNames[$version];
            }
        }

        return array_merge(parent::getMessageParameters(), [
            'version' => implode(', ', $versions),
        ]);
    }
}
