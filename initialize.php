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

if (isset($_SERVER['HTTP_REFERER']) && !defined('ORG_REFERER')) {
    define('ORG_REFERER',$_SERVER['HTTP_REFERER']);
}

/**
 * [1] Just to make sure the WBCE autoloader will find the module classes
 *     (As this one line is missing in the "inizialize" file of the root.)
 */
WbAuto::AddDir(WB_PATH."/modules/");
