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

$top = 10;
$r = $stats->getVisitors(100);

$pages_cloud = $stats->accessPagesCloud();
$second_cloud = $stats->accessSecondCloud();
$WS = $stats->accessLanguage();

?>
<div class="sysmenu small">
  <a href="#" class="pop" data-sec="pages"><?php echo $WS['PAGETOP']  ?></a>
  <a href="#" class="pop" data-sec="entry"><?php echo $WS['ENTRYTOP']  ?></a>
  <a href="#" class="pop" data-sec="exit"><?php echo $WS['EXITTOP']  ?></a>
  <a href="#" class="pop" data-sec="referer"><?php echo $WS['REFTOP'] ?></a>
  <a href="#" class="pop" data-sec="keys"><?php echo $WS['KEYSTOP']  ?></a>
  <a href="#" class="pop" data-sec="lang"><?php echo $WS['LANGTOP']  ?></a>
  <a href="#" class="pop" data-sec="browser"><?php echo $WS['BROWSERTOP']  ?></a>
  <a href="#" class="pop" data-sec="os"><?php echo $WS['OSTOP']  ?></a>
  <a href="#" class="pop" data-sec="countries"><?php echo $WS['COUNTRYTOP']  ?></a>
  <a href="#" class="pop" data-sec="location"><?php echo $WS['LOCTOP']  ?></a>
</div>

// [1] Pages
<div class="full h265" id="pages">
    <h3><span><?php echo $WS['TOP'] . ' ' . $top . ' - ' ?></span><?php echo $WS['PAGETOP'] ?></h3>
<?php

    echo $stats->handleTwigTemplate(
        [
            'top'       => $top,
            'fbar'      => $WS['NUMBER'],
            'title'     => $WS['PAGES'],
            'percent'   => $WS['PERCENT'],
            'data'      => $r['pages'],
            'ws_REQUESTS' => $WS['REQUESTS']
        ]
    );
?>
</div>

// [2] Entries
<div class="middle h265" id="entry">
    <h3><span><?php echo $WS['TOP'] . ' ' . $top . ' - ' ?></span><?php echo $WS['ENTRYTOP'] ?></h3>
<?php

    echo $stats->handleTwigTemplate(
        [
            'top'       => $top,
            'fbar'      => $WS['NUMBER'],
            'title'     => $WS['PAGES'],
            'percent'   => $WS['PERCENT'],
            'data'      => $r['entry'],
            'ws_REQUESTS' => $WS['VISITORS'], // [1]
            'h265'        => true
        ]
    );
?>
</div>

// [3] Exit pages
<div class="middle h265" id="exit">
    <h3><span><?php echo $WS['TOP'].' '.$top.' - ' ?></span><?php echo $WS['EXITTOP'] ?></h3>
<?php

    echo $stats->handleTwigTemplate(
        [
            'top'       => $top,
            'fbar'      => $WS['NUMBER'],
            'title'     => $WS['PAGES'],
            'percent'   => $WS['PERCENT'],
            'data'      => $r['exit'],
            'ws_REQUESTS' => $WS['VISITORS'], // [1]
            'h265'        => true
        ]
    );
?>
</div>


<div style="clear:both"></div>

// [4] REFERER
<div class="middle h265" id="referer">
    <h3><span><?php echo $WS['TOP'].' '.$top.' - '?></span><?php echo $WS['REFTOP'] ?></h3>
<?php

    echo $stats->handleTwigTemplate(
        [
            'top'       => $top,
            'fbar'      => $WS['NUMBER'],
            'title'     => $WS['REFERER'],
            'percent'   => $WS['PERCENT'],
            'data'      => $r['referer'],
            'ws_REQUESTS' => $WS['VISITORS'], // [1]
            'h265'        => true
        ]
    );
?>
</div>

// [5] Keywords
<div class="middle h265" id="keys">
    <h3><span><?php echo $WS['TOP'].' '.$top.' - ' ?></span><?php echo $WS['KEYSTOP'] ?></h3>
<?php
    echo $stats->handleTwigTemplate(
        [
            'top'       => $top,
            'fbar'      => $WS['NUMBER'],
            'title'     => $WS['KEYWORDS'],
            'percent'   => $WS['PERCENT'],
            'data'      => $r['keyword'],
            'ws_REQUESTS' => $WS['VISITORS'], // [1]
            'h265'        => true
        ]
    );
?>
</div>

<div style="clear:both"></div>
<div class="third h265" id="lang">
    <h3><span><?php echo $WS['TOP'].' '.$top.' - ' ?></span><?php echo $WS['LANGTOP'] ?></h3>
<?php
    echo $stats->handleTwigTemplate(
        [
            'top'       => $top,
            'fbar'      => $WS['NUMBER'],
            'title'     => $WS['LANGUAGES'],
            'percent'   => $WS['PERCENT'],
            'data'      => $r['language'],
            'ws_REQUESTS' => $WS['VISITORS'], // [1]
            'h265'        => true
        ]
    );
?>
</div>

<div class="third h265" id="browser">
    <h3><span><?php echo $WS['TOP'].' '.$top.' - ' ?></span><?php echo $WS['BROWSERTOP'] ?></h3>
<?php
    echo $stats->handleTwigTemplate(
        [
            'top'       => $top,
            'fbar'      => $WS['NUMBER'],
            'title'     => $WS['BROWSER'],
            'percent'   => $WS['PERCENT'],
            'data'      => $r['browser'],
            'ws_REQUESTS' => $WS['VISITORS'], // [1]
            'h265'        => true
        ]
    );
?>
</div>

<div class="third h265" id="os">
    <h3><span><?php echo $WS['TOP'].' '.$top.' - ' ?></span><?php echo $WS['OSTOP'] ?></h3>
<?php
    echo $stats->handleTwigTemplate(
        [
            'top'       => $top,
            'fbar'      => $WS['NUMBER'],
            'title'     => $WS['OS'],
            'percent'   => $WS['PERCENT'],
            'data'      => $r['os'],
            'ws_REQUESTS' => $WS['VISITORS'], // [1]
            'h265'        => true
        ]
    );
?>
</div>

<div style="clear:both"></div>

<div class="middle h265" id="countries">
    <h3><span><?php echo $WS['TOP'].' '.$top.' - ' ?></span><?php echo $WS['COUNTRYTOP'] ?></h3>
<?php
    echo $stats->handleTwigTemplate(
        [
            'top'       => $top,
            'fbar'      => $WS['NUMBER'],
            'title'     => $WS['COUNTRIES'],
            'percent'   => $WS['PERCENT'],
            'data'      => $r['country'],
            'ws_REQUESTS' => $WS['VISITORS'], // [1]
            'h265'        => true
        ]
    );
?>
</div>

<div class="middle h265" id="location">
    <h3><span><?php echo $WS['TOP'].' '.$top.' - ' ?></span><?php echo $WS['LOCTOP'] ?></h3>
<?php
    echo $stats->handleTwigTemplate(
        [
            'top'       => $top,
            'fbar'      => $WS['NUMBER'],
            'title'     => $WS['LOCATIONS'],
            'percent'   => $WS['PERCENT'],
            'data'      => $r['location'],
            'ws_REQUESTS' => $WS['VISITORS'], // [1]
            'h265'        => true
        ]
    );
?>
</div>

<div style="clear:both"></div>

<div class="middle h265">
    <h3><?php echo $WS['PAGES_CLOUD'] ?></h3>
    <div class="cloud-container">
        <?php
        if(isset($r['pageviews']) && is_array($r['pageviews']))
        {
            $tmp_1 = $r['pageviews'];
            $tmp = $stats->shuffle_assoc($tmp_1);
            $maxval = max($tmp)+1; $minfont = 10; $maxfont = 28;
            if (log($maxval)>0)
            {
                foreach ($tmp as $key => $data)
                {
                    $fontsize = round((log($data) / log($maxval)) * ($maxfont - $minfont) + $minfont);
                    if($data)
                    {
                        echo '<span title="'.$data.' '.$WS['VISITORS'].'" style="font-size:'.$fontsize.'px" class="expand wordcloud">'.$pages_cloud[$key].'</span> ';
                    }
                }
            }
        }
        ?>
    </div>
</div>
<div class="middle h265">
    <h3><?php echo $WS['SECONDS_CLOUD'] ?></h3>
    <div class="cloud-container">
        <?php
        if(isset($r['seconds']) && is_array($r['seconds']))
        {
            $tmp_2 = $r['seconds'];
            $tmp = $stats->shuffle_assoc($tmp_2);
            $maxval = max($tmp)+1;
            $minfont = 10;
            $maxfont = 28;
            if (log($maxval) > 0)
            {
                foreach ($tmp as $key => $data)
                {
                    $fontsize = round((log($data) / log($maxval)) * ($maxfont - $minfont) + $minfont);
                    if ($data)
                    {
                        echo '<span title="' . $data . ' ' . $WS['VISITORS'] . '" style="font-size:' . $fontsize . 'px" class="expand wordcloud">' . $second_cloud[$key] . '</span> ';
                    }
                }
            }
        }
        ?>
    </div>
</div>
