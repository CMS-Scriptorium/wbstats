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

defined('WB_PATH') OR die(header('Location: ../index.php'));

class Request
{

    public static function getValue(string $name, string $what = "str", string $where='GET'): string
    {
        $filter = self::getFilter($where);

        return filter_input(
            $filter,
            $name,
            FILTER_VALIDATE_REGEXP, 
                ['options' => [
                        "regexp" => self::getPatternByWhat($what),
                        "default" => null
                    ] 
                ]
            ) ?? "";
    }

    static protected function getPatternByWhat(string $what): string
    {
        $retVal = "//";
        switch (strtolower($what))
        {
            case "string":
            case "str":
                $retVal = "/^[a-z0-9]{4,}$/";
                break;

            default:
                $retVal = "/^[A-Za-z0-9]{2,}$/";
                break;
        }
        
        return $retVal;
    }

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
