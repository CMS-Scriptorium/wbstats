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

// Add to the top of your template (within the <?php)
// include ( WB_PATH.'/modules/wbstats/count.php');

// Add to the /config.php, just before the initialize line
// $referer = $_SERVER['HTTP_REFERER'];

require_once __DIR__ . '/core/Counter.php';
new wbstats\core\Counter();
