<?php

declare(strict_types=1);

/**
 *
 * @category        admintool
 * @package         wbstats
 * @author          Ruud Eisinga - dev4me.com
 * @link            https://dev4me.com/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.x / WBCE 1.6.x
 * @requirements    PHP 8.3 and higher
 * @version         0.2.6.0
 * @lastmodified    September 11, 2026
 *
 */

namespace wbstats\core;

/**
 * This is just a temporary solution — until WBCE 1.7.0 is officially released!
 *
 */
class Request
{
    /**
     * Get a value from the $_GET, $_POST, etc. superglobal var.
     *
     * @param string $name      A valid name of the var.
     * @param string $what      What type of expected value (e.g. "str")
     * @param string $where     $_POST or $_GET, default is "get" - at this time.
     *
     * @return string           The value as string.
     */
    public static function getValue(
        string $name,
        string $what  = "str",
        string $where = "GET",
        array  $range = []
    ): mixed
    {
        $filter = self::getFilter($where);

        $result = filter_input(
            $filter,
            $name,
            FILTER_VALIDATE_REGEXP, 
                ['options' => [
                        "regexp" => self::getPatternByWhat($what),
                        "default" => null
                    ] 
                ]
            ) ?? "";

        if (!empty($range))
        {
            self::handleRange($result, $range);
        }

        return self::coerceReturn($result, $what);
    }

    static protected function handleRange(string|int &$value, array $range): void
    {
        if (isset($range['min']))
        {
            if ($value < $range['min'])
            {
                $value = $range['default'] ?? $range['min'];
            }
        }

        if (isset($range['max']))
        {
            if ($value > $range['max'])
            {
                $value = $range['default'] ?? $range['max'];
            }
        }
    }

    static protected function coerceReturn(mixed $value, string $type): mixed
    {
        switch ($type)
        {
            case 's':
            case 'str':
            case 'string':
                return (string) $value;

            case 'i':
            case 'int':
            case 'integer':
                return (int) $value;
            
            default:
                return $value;
        }
    }

    /**
     * 
     * @param string $what
     * @return string
     */
    static protected function getPatternByWhat(string $what): string
    {
        $retVal = "//";
        switch (strtolower($what))
        {
            case "string":
            case "str":
                $retVal = "/^[a-z0-9]{4,}$/";
                break;

            case "int":
            case "integer":
                $retVal = "/^[0-9\+\-]+$/";
                break;

            default:
                $retVal = "/^[A-Za-z0-9]{2,}$/";
                break;
        }
        
        return $retVal;
    }

    /**
     * 
     * @param string $where
     * @return int
     */
    static protected function getFilter(string $where): int 
    {
        switch (strtolower($where))
        {
            case 'get':
                $filter = INPUT_GET;
                break;

            case 'post':
                $filter = INPUT_POST;
                break;

            case 'server':
                $filter = INPUT_SERVER;
                break;

            case 'session':
                $filter = INPUT_SESSION;
                break;

            default:
                $filter = INPUT_GET;
                break;
        }
        
        return $filter;
    }
}
