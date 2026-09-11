<?php
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

defined('WB_PATH') OR die(header('Location: ../index.php'));

// Aldus [2026-09-11] Read/use the (Module-)Config!
require_once __DIR__ . "/core/Config.php";

$myConstants = wbstats\core\Config::getConstants();

foreach ($myConstants as $key => $value)
{
    if (str_starts_with($key, "TABLE_"))
    {
        $database->query("DROP TABLE IF EXISTS `" . $value . "`");
    }
}
