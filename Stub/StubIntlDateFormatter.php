<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Locale\Stub;

use Symfony\Component\Locale\Stub\StubLocale;
use Symfony\Component\Locale\Exception\NotImplementedException;
use Symfony\Component\Locale\Exception\MethodNotImplementedException;
use Symfony\Component\Locale\Exception\MethodArgumentValueNotImplementedException;

/**
 * Provides a stub IntlDateFormatter for the 'en' locale.
 *
 * @author Igor Wiedler <igor@wiedler.ch>
 */
class StubIntlDateFormatter
{
    /**
     * Constants defined by the intl extension, not class constants in IntlDateFormatter
     * TODO: remove if the Form component drop the call to the intl_is_failure() function
     *
     * @see StubIntlDateFormatter::getErrorCode()
     * @see StubIntlDateFormatter::getErrorMessage()
     */
    const U_ZERO_ERROR = 0;
    const U_ZERO_ERROR_MESSAGE = 'U_ZERO_ERROR';

    /* date/time format types */
    const NONE = -1;
    const FULL = 0;
    const LONG = 1;
    const MEDIUM = 2;
    const SHORT = 3;

    /* calendar formats */
    const TRADITIONAL = 0;
    const GREGORIAN = 1;

    /**
     * Patterns used to format the date when no pattern
     * is provided
     */
    private $defaultDateFormats = array(
        self::NONE      => '',
        self::FULL      => 'EEEE, LLLL d, y',
        self::LONG      => 'LLLL d, y',
        self::MEDIUM    => 'LLL d, y',
        self::SHORT     => 'M/d/yy',
    );

    /**
     * Patterns used to format the time when no pattern
     * is provided
     */
    private $defaultTimeFormats = array(
        self::FULL      => 'h:mm:ss a zzzz',
        self::LONG      => 'h:mm:ss a z',
        self::MEDIUM    => 'h:mm:ss a',
        self::SHORT     => 'h:mm a',
    );

    private $datetype;
    private $timetype;
    private $pattern;

    /**
     * @var DateTimeZone
     */
    private $dateTimeZone;

    /**
     * Constructor
     *
     * @param  string  $locale   The locale code
     * @param  int     $datetype Type of date formatting, one of the format type constants
     * @param  int     $timetype Type of time formatting, one of the format type constants
     * @param  string  $timezone Timezone identifier
     * @param  int     $calendar Calendar to use for formatting or parsing; default is Gregorian.
     *                           One of the calendar constants.
     * @param  string  $pattern  Optional pattern to use when formatting.
     * @see    http://userguide.icu-project.org/formatparse/datetime
     * @throws MethodArgumentValueNotImplementedException  When $locale different than 'en' is passed
     * @throws MethodArgumentValueNotImplementedException  When $calendar different than GREGORIAN is passed
     */
    public function __construct($locale, $datetype, $timetype, $timezone = null, $calendar = self::GREGORIAN, $pattern = null)
    {
        if ('en' != $locale) {
            throw new MethodArgumentValueNotImplementedException(__METHOD__, 'locale', $locale, 'Only the \'en\' locale is supported');
        }

        if (self::GREGORIAN != $calendar) {
            throw new MethodArgumentValueNotImplementedException(__METHOD__, 'calendar', $calendar, 'Only the GREGORIAN calendar is supported');
        }

        $this->datetype = $datetype;
        $this->timetype = $timetype;

        $this->setPattern($pattern);
        $this->setTimeZoneId($timezone);
    }

    /**
     * Format the date/time value (timestamp) as a string
     *
     * @param  mixed         $timestamp   Unix timestamp to format
     * @return string                   The formatted value
     * @throws NotImplementedException  If one of the formatting characters is not implemented
     */
    public function format($timestamp)
    {
        if (!is_int($timestamp)) {
            throw new MethodArgumentValueNotImplementedException(__METHOD__, 'timestamp', $timestamp, 'Only the integer unix timestamps are supported');
        }

        $dateTime = $this->createDateTime($timestamp);
        $dateTime->setTimestamp($timestamp);
        $dateTime->setTimezone($this->dateTimeZone);

        $transformer = new DateFormat\FullTransformer($this->getPattern(), $this->getTimeZoneId());
        $formatted = $transformer->format($dateTime);

        return $formatted;
    }

    /**
     * Returns the formatter's calendar
     *
     * @return int              The calendar being used by the formatter
     */
    public function getCalendar()
    {
        return self::GREGORIAN;
    }

    /**
     * Returns the formatter's datetype
     *
     * @return int              The current value of the formatter
     */
    public function getDateType()
    {
        return $this->datetype;
    }

    /**
     * Returns formatter's last error code. Always returns the U_ZERO_ERROR class constant value
     *
     * @return int  The error code from last formatter call
     */
    public function getErrorCode()
    {
        return self::U_ZERO_ERROR;
    }

    /**
     * Returns formatter's last error message. Always returns the U_ZERO_ERROR_MESSAGE class constant value
     *
     * @return string  The error message from last formatter call
     */
    public function getErrorMessage()
    {
        return self::U_ZERO_ERROR_MESSAGE;
    }

    /**
     * Returns the formatter's locale
     *
     * @param  int      $type     The locale name type to return between valid or actual (StubLocale::VALID_LOCALE or StubLocale::ACTUAL_LOCALE, respectively)
     * @return string             The locale name used to create the formatter
     */
    public function getLocale($type = StubLocale::ACTUAL_LOCALE)
    {
        return 'en';
    }

    /**
     * Returns the formatter's pattern
     *
     * @return string        The pattern string used by the formatter
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Returns the formatter's time type
     *
     * @return string        The time type used by the formatter
     */
    public function getTimeType()
    {
        return $this->timetype;
    }

    /**
     * Returns the formatter's timezone identifier
     *
     * @return string        The timezone identifier used by the formatter
     */
    public function getTimeZoneId()
    {
        return $this->dateTimeZone->getName();
    }

    /**
     * Returns whether the formatter is lenient
     *
     * @return string        The timezone identifier used by the formatter
     * @throws MethodNotImplementedException
     */
    public function isLenient()
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * Parse string to a field-based time value
     *
     * @param  string   $value      String to convert to a time value
     * @param  int      $position   Position at which to start the parsing in $value (zero-based).
     *                              If no error occurs before $value is consumed, $parse_pos will
     *                              contain -1 otherwise it will contain the position at which parsing
     *                              ended. If $parse_pos > strlen($value), the parse fails immediately.
     * @return string               Localtime compatible array of integers: contains 24 hour clock value in tm_hour field
     * @throws MethodNotImplementedException
     */
    public function localtime($value, &$position = 0)
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * Parse string to a timestamp value
     *
     * @param  string   $value      String to convert to a time value
     * @param  int      $position   Position at which to start the parsing in $value (zero-based).
     *                              If no error occurs before $value is consumed, $parse_pos will
     *                              contain -1 otherwise it will contain the position at which parsing
     *                              ended. If $parse_pos > strlen($value), the parse fails immediately.
     * @return string               Parsed value as a timestamp
     */
    public function parse($value, &$position = 0)
    {
        $dateTime = $this->createDateTime(0);
        $transformer = new DateFormat\FullTransformer($this->getPattern(), $this->getTimeZoneId());
        return $transformer->parse($dateTime, $value);
    }

    /**
     * Set the formatter's calendar
     *
     * @param  string  $calendar  The calendar to use. Default is IntlDateFormatter::GREGORIAN.
     * @return bool               true on success or false on failure
     * @throws MethodNotImplementedException
     */
    public function setCalendar($calendar)
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * Set the leniency of the parser
     *
     * Define if the parser is strict or lenient in interpreting inputs that do not match the pattern
     * exactly. Enabling lenient parsing allows the parser to accept otherwise flawed date or time
     * patterns, parsing as much as possible to obtain a value. Extra space, unrecognized tokens, or
     * invalid values ("February 30th") are not accepted.
     *
     * @param  bool  $lenient     Sets whether the parser is lenient or not, default is false (strict)
     * @return bool               true on success or false on failure
     * @throws MethodNotImplementedException
     */
    public function setLenient($lenient)
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * Set the formatter's pattern
     *
     * @param  strubg  $pattern   A pattern string in conformance with the ICU IntlDateFormatter documentation
     * @return bool               true on success or false on failure
     * @see    http://userguide.icu-project.org/formatparse/datetime
     */
    public function setPattern($pattern)
    {
        if (null === $pattern) {
            $pattern = $this->getDefaultPattern();
        }

        $this->pattern = $pattern;
    }

    /**
     * Set the formatter's timezone identifier
     *
     * @param  string  $timeZoneId      The time zone ID string of the time zone to use.
     *                                  If NULL or the empty string, the default time zone for the
     *                                  runtime is used.
     * @return bool                     true on success or false on failure
     */
    public function setTimeZoneId($timeZoneId)
    {
        try {
            $this->dateTimeZone = new \DateTimeZone($timeZoneId);
        } catch (\Exception $e) {
            $this->dateTimeZone = new \DateTimeZone('UTC');
        }
    }

    protected function createDateTime($timestamp)
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);
        $dateTime->setTimezone($this->dateTimeZone);

        return $dateTime;
    }

    protected function getDefaultPattern()
    {
        $patternParts = array();
        if (self::NONE !== $this->datetype) {
            $patternParts[] = $this->defaultDateFormats[$this->datetype];
        }
        if (self::NONE !== $this->timetype) {
            $patternParts[] = $this->defaultTimeFormats[$this->timetype];
        }
        $pattern = implode(' ', $patternParts);

        return $pattern;
    }

    /**
     * Static constructor
     *
     * @param  string  $locale   The locale code
     * @param  int     $datetype Type of date formatting, one of the format type constants
     * @param  int     $timetype Type of time formatting, one of the format type constants
     * @param  string  $timezone Timezone identifier
     * @param  int     $calendar Calendar to use for formatting or parsing; default is Gregorian.
     *                           One of the calendar constants.
     * @param  string  $pattern  Optional pattern to use when formatting.
     * @see    http://userguide.icu-project.org/formatparse/datetime
     * @throws MethodArgumentValueNotImplementedException  When $locale different than 'en' is passed
     */
    static public function create($locale, $datetype, $timetype, $timezone = null, $calendar = self::GREGORIAN, $pattern = null)
    {
        return new self($locale, $datetype, $timetype, $timezone, $calendar, $pattern);
    }
}
