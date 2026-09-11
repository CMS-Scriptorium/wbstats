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

$lang = (dirname(__FILE__)) . '/languages/' . LANGUAGE . '.php';
require_once(!file_exists($lang) ? (dirname(__FILE__)) . '/languages/EN.php' : $lang );

require_once 'info.php';
require_once __DIR__ . '/core/Stats.php';

$baseURL = ADMIN_URL . '/admintools/tool.php?tool=wbstats';

$admintool_url        = ADMIN_URL . '/admintools/index.php';
$module_link          = $baseURL;
$module_overview_link = $baseURL . '&show=overview';
$module_visitors_link = $baseURL . '&show=visitors';
$module_history_link  = $baseURL . '&show=history';
$module_live_link     = $baseURL . '&show=live';
$module_log_link      = $baseURL . '&show=logbook';
$module_campaign_link = $baseURL . '&show=campaigns';
$module_cfg_link      = $baseURL . '&show=config';
$module_help_link     = $baseURL . '&show=help';

require_once "head.php";

$toShow = filter_input(INPUT_GET, "show") ?? "";

if (!$check = $database->get_one("SELECT sum(user) visitors FROM " . wbstats\core\Config::TABLE_DAY))
{
    $toShow = "help";

}

switch ($toShow)
{
    case 'overview':
    case 'visitors':
    case 'history':
    case 'live':
    case 'logbook':
    case 'campaigns':
    case 'help':
        $requireFile = $toShow . ".php";
        break;

    case 'config':
        $requireFile = "setconfig.php";
        break;

    default:
        $requireFile = "overview.php";
        break;
}

require __DIR__ . "/tabs/" . $requireFile;
