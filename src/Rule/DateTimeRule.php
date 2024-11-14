<?php

declare(strict_types=1);

namespace Danek\Validator\Rule;

use Danek\Validator\Rule;

class DateTimeRule extends Rule
{
    const INVALID_VALUE = 'Datetime::INVALID_VALUE';

    /** @var array */
    protected $messageTemplates = [
        self::INVALID_VALUE => '{{name}} must be a valid date',
    ];

    /** @var string */
    protected $format;

    public function __construct(string $format = null)
    {
        $this->format = $format;
    }

    /**
     * @param mixed $value
     */
    public function validate($value): bool
    {
        if (!($this->datetime($value, $this->format) instanceof \DateTime)) {
            return $this->addError(self::INVALID_VALUE);
        }
        return true;
    }

    /**
     * @param mixed $value
     * @return \DateTime|false
     */
    protected function datetime($value, ?string $format = null)
    {
        if ($format !== null) {
            $dateTime = date_create_from_format($format, (string)$value);

            if ($dateTime instanceof \DateTime) {
                return $this->checkDate($dateTime, $format, $value);
            }
            return false;
        }
        try {
            return new \DateTime($value); //return date_create($value);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $value
     * @return \DateTime|false
     */
    protected function checkDate(\DateTime $dateTime, string $format, $value)
    {
        $equal = $dateTime->format($format) === (string)$value;

        $warningCount = $dateTime->getLastErrors()['warning_count'] ?? 0;
        if ($warningCount === 0 && $equal) {
            return $dateTime;
        }
        return false;
    }
}
