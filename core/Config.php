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

use const TABLE_PREFIX;

defined('WB_PATH') OR die(header('Location: ../index.php'));

class Config
{
    public const string TABLE_DAY     = TABLE_PREFIX . 'mod_wbstats_day';
    public const string TABLE_IPS     = TABLE_PREFIX . 'mod_wbstats_ips';
    public const string TABLE_PAGES   = TABLE_PREFIX . 'mod_wbstats_pages';
    public const string TABLE_REF     = TABLE_PREFIX . 'mod_wbstats_ref';
    public const string TABLE_KEY     = TABLE_PREFIX . 'mod_wbstats_keywords';
    public const string TABLE_LANG    = TABLE_PREFIX . 'mod_wbstats_lang';
    public const string TABLE_BROWSER = TABLE_PREFIX . 'mod_wbstats_browser';
    public const string TABLE_HIST    = TABLE_PREFIX . 'mod_wbstats_hist';
    public const string TABLE_LOC     = TABLE_PREFIX . 'mod_wbstats_loc';
    public const string TABLE_UTM     = TABLE_PREFIX . 'mod_wbstats_utm';
    public const string TABLE_CFG     = TABLE_PREFIX . 'mod_wbstats_cfg';
    public const string TABLE_SHOP    = TABLE_PREFIX . 'mod_wbstats_shop';

    protected const string PLATFORM        = 'platform';
    protected const string BROWSER         = 'browser';
    protected const string BROWSER_VERSION = 'version';

    /**
     * Get the constants of the further inherited class.
     *
     * @return array
     */
    public static function getConstants(): array
    {
        // "static::class" here does the magic
        try {
            $reflectionClass = new \ReflectionClass(static::class);
            return $reflectionClass->getConstants();

        } catch ( \ReflectionException $e) {
            \Subway\core\tools\data::display($e->getMessage());
        }
        return [];
    }
}
