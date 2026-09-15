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
 
// prevent this file from being accessed directly
if (!defined('WB_PATH'))
{
    header('Location: ../../index.php');
    die();
}

$c = $stats->getCampaigns();

foreach ($c as $campaign => $data)
{
    foreach ($data as $content => $val)
    {
        if (stripos($content, "Not identified") === false)
        {
            echo '<div class="full">';
            echo '<h3>' . $campaign . ' -> ' . $content . '</h3>';

            echo '<table aria-hidden="true" class="res" width="100%" border="0" cellpadding="5" cellspacing="0">';
            echo '<tr>';
            echo '<th style="width:60px;">Source</td>';
            echo '<th style="width:190px;">Medium</td>';
            echo '<th>Content</td>';
            echo '<th style="text-align:center;width:110px">First date</td>';
            echo '<th style="text-align:center;width:110px">Last date</td>';
            echo '<th style="text-align:center;width:50px">Visits</td>';
            echo '<th style="text-align:center;width:50px">Bounces</td>';
            echo '<th style="text-align:center;width:50px">Pages</td>';
            echo '<th style="text-align:center;width:50px">Avg</td>';
            echo '<tr>';
            foreach ($val as $medium => $detail)
            {
                echo '<tr>';
                echo '<td>' . $detail['source'] . '</td>';
                echo '<td>' . $medium . '</td>';
                echo '<td style="white-space:nowrap;">' . $content . '</td>';
                echo '<td style="text-align:center;">' . fdate($detail['first']) . '</td>';
                echo '<td style="text-align:center;">' . fdate($detail['last']) . '</td>';
                echo '<td style="text-align:center;">' . $detail['totalcount'] . '</td>';
                echo '<td style="text-align:center;">' . $detail['bounces'] . ' <small>(' . $detail['bounce_perc'] . '%)</small></td>';
                echo '<td style="text-align:center;">' . $detail['pages'] . '</td>';
                echo '<td style="text-align:center;">' . $detail['pages_visit'] . '</td>';
                echo '<tr>';
            }
            echo '</table>';
            echo '</div>';
        }
    }
}

function fdate($d)
{
    $d = substr($d, 0, 4) . '-' . substr($d, 4, 2) . '-' . substr($d, 6, 2);

    return $d;
}
