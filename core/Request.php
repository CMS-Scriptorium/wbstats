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
 * @lastmodified    September 12, 2026
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
     * Filter type constants for memoization
     */
    private static array $filterMap = [
        'get'     => INPUT_GET,
        'post'    => INPUT_POST,
        'server'  => INPUT_SERVER
    ];

    /**
     * Regex pattern cache
     */
    private static array $patternCache = [
        'str'     => '/^[a-z0-9]{4,}$/',
        'int'     => '/^[0-9\+\-]+$/',
        'default' => '/^[A-Za-z0-9]{2,}$/',
    ];

    /**
     * Get a value from the $_GET, $_POST, etc. superglobal var.
     *
     * @param string $name      A valid name of the var.
     * @param string $what      What type of expected value (e.g. "str")
     * @param string $where     $_POST or $_GET, default is "get" - at this time.
     * @param array  $range     Optional min/max constraints
     *
     * @return string|int|null  The validated value or null if invalid.
     */
    public static function getValue(
        string $name,
        string $what  = "str",
        string $where = "GET",
        array  $range = []
    ): string|int|null
    {
        $filter = self::$filterMap[strtolower($where)] ?? INPUT_GET;
        $pattern = self::$patternCache[strtolower($what)] ?? self::$patternCache['default'];

        $result = filter_input(
            $filter,
            $name,
            FILTER_VALIDATE_REGEXP,
            ['options' => [
                'regexp' => $pattern,
                'default' => null
            ]]
        );

        if (!empty($range))
        {
            self::applyRange($result, $range);
        }

        return self::coerceReturn($result, $what);
    }

    /**
     * Apply min/max range constraints to a value
     *
     * @param  string|int  $value  Call by reference!
     * @param  array       $range
     *
     * @return void
     */
    private static function applyRange(string|int &$value, array $range): void
    {
        $numValue = (int) $value;

        if (isset($range['min']) && $numValue < $range['min'])
        {
            $value = $range['default'] ?? $range['min'];
        
        } elseif (isset($range['max']) && $numValue > $range['max'])
        {
            $value = $range['default'] ?? $range['max'];
        }
    }

    /**
     * Coerce return value to the specified type
     * 
     * @param  mixed     $value  Any valid value
     * @param  string    $what   Any valid "type" - keep in mind  
     *                           we can only handle integer and string here
     * @return string|int
     */
    private static function coerceReturn(mixed $value, string $what): string|int
    {
        $type = strtolower($what);

        if ($type === 'i' || $type === 'int' || $type === 'integer')
        {
            return (int) $value;
        }

        return (string) $value;
    }
}
