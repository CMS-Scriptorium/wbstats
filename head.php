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
if (defined('WB_PATH') == false)
{
    die("Cannot access this file directly");
}

$stats = new wbstats\core\Stats();

$show_campaign = $stats->hasCampaigns() ? '' : 'style="display:none;"';

?>
<script type="text/javascript" src="<?php echo WB_URL ?>/modules/wbstats/js/jquery.poshytip.js"></script>
<div id="loading" class="box" style="display:none;"><?php echo $WS['PLEASEWAIT'] ?></div>
<div id="container">
<div class="sysmenu">
  <div style="text-align:right;font-weight:900;color:#888; margin:-30px 0 15px;">WBstats v<?=$module_version ?></div>
  <a href="<?php echo $module_overview_link  ?>"><?php echo $WS['MENU1'] ?></a>
  <a href="<?php echo $module_live_link  ?>"><?php echo $WS['MENU4'] ?></a>
  <a href="<?php echo $module_log_link  ?>"><?php echo $WS['MENU7'] ?></a>
  <a href="<?php echo $module_visitors_link  ?>"><?php echo $WS['MENU2'] ?></a>
  <a href="<?php echo $module_history_link  ?>"><?php echo $WS['MENU3'] ?></a>
  <a <?=$show_campaign?> href="<?php echo $module_campaign_link  ?>"><?php echo $WS['MENU8'] ?></a>
  <a style="float:right;margin-left:5px;" href="<?php echo $module_help_link  ?>"><?php echo $WS['MENU6'] ?></a>
  <a style="float:right;margin-left:5px;" href="<?php echo $module_cfg_link  ?>"><?php echo $WS['MENU5'] ?></a>
</div>
