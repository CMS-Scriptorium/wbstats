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

/**
 *	Must include code to stop this file being access directly
 */
if (defined('WB_PATH') == false) die("Cannot access this file directly");

global $WS;
$mpath = WB_PATH.'/modules/wbstats/';
$lang = $mpath . '/languages/' . LANGUAGE . '.php';
require_once(!file_exists($lang) ? $mpath . '/languages/EN.php' : $lang );

// Aldus [2026-09-11] No idea why on earth we need this one here ...
require_once $mpath .'/info.php';

// This one is obsolet in 1.7.0?
require_once $mpath . '/core/Stats.php';
require_once $mpath . '/core/Request.php';

$stats = new wbstats\core\Stats();

$module_overview_link   = '?show=overview';
$module_visitors_link   = '?show=visitors';
$module_history_link    = '?show=history';
$module_live_link       = '?show=live';
$module_log_link        = '?show=logbook';
?>
<script type="text/javascript" src="<?php echo WB_URL ?>/modules/wbstats/backend.js"></script>
<script type="text/javascript" src="<?php echo WB_URL ?>/modules/wbstats/js/jquery.poshytip.js"></script>
<div id="container" class="frontend">
<div class="sysmenu">
  <a href="<?php echo $module_overview_link  ?>"><?php echo $WS['MENU1'] ?></a>
  <a href="<?php echo $module_live_link  ?>"><?php echo $WS['MENU4'] ?></a>
  <a href="<?php echo $module_log_link  ?>"><?php echo $WS['MENU7'] ?></a>
  <a href="<?php echo $module_visitors_link  ?>"><?php echo $WS['MENU2'] ?></a>
  <a href="<?php echo $module_history_link  ?>"><?php echo $WS['MENU3'] ?></a>
</div>
<?php

$toShow = wbstats\core\Request::getValue("show");

switch ($toShow)
{
    case 'overview':
    case 'visitors':
    case 'history':
    case 'live':
    case 'logbook':
    case 'campaigns':
        $requireFile = $toShow . ".php";
        break;

    default:
        $requireFile = "overview.php";
        break;
}

require __DIR__ . "/tabs/" . $requireFile;

?>
</div>