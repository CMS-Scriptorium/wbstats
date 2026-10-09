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

<div class="full h265" id="pages">
<?php
// [1]
    echo $stats->handleTwigTemplate(
        [
            'ws_top'    => $WS['TOP'],
            'ws_table'  => $WS['PAGETOP'],      // c1
            'top'       => $top,
            'fbar'      => $WS['NUMBER'],
            'title'     => $WS['PAGES'],        // c2
            'percent'   => $WS['PERCENT'],
            'data'      => $r['pages'],         // c3
            'ws_REQUESTS' => $WS['REQUESTS']
        ]
    );
?>
</div>

<div class="middle h265" id="entry">
<?php
// [2]
    echo $stats->handleTwigTemplate(
        [
            'ws_top'    => $WS['TOP'],
            'ws_table'  => $WS['ENTRYTOP'],      // c1
            'top'       => $top,
            'fbar'      => $WS['NUMBER'],
            'title'     => $WS['PAGES'],         // c2
            'percent'   => $WS['PERCENT'],
            'data'      => $r['entry'],          // c3
            'ws_REQUESTS' => $WS['VISITORS'],    // [1]
            'h265'        => true
        ]
    );
?>
</div>

<div class="middle h265" id="exit">
<?php
// [3]
    echo $stats->handleTwigTemplate(
        [
            'ws_top'    => $WS['TOP'],
            'ws_table'  => $WS['EXITTOP'],
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

<div class="middle h265" id="referer">
<?php
// [4]
    echo $stats->handleTwigTemplate(
        [
            'ws_top'    => $WS['TOP'],
            'ws_table'  => $WS['REFTOP'],
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

<div class="middle h265" id="keys">
<?php
// [5]
    echo $stats->handleTwigTemplate(
        [
            'ws_top'    => $WS['TOP'],
            'ws_table'  => $WS['KEYSTOP'],
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
<?php
// [6]
    echo $stats->handleTwigTemplate(
        [
            'ws_top'    => $WS['TOP'],
            'ws_table'  => $WS['LANGTOP'],
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
<?php
// [7]
    echo $stats->handleTwigTemplate(
        [
            'ws_top'    => $WS['TOP'],
            'ws_table'  => $WS['BROWSERTOP'],
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
<?php
// [8]
    echo $stats->handleTwigTemplate(
        [
            'ws_top'    => $WS['TOP'],
            'ws_table'  => $WS['OSTOP'],
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
<?php
// [9]
    echo $stats->handleTwigTemplate(
        [
            'ws_top'    => $WS['TOP'],
            'ws_table'  => $WS['COUNTRYTOP'],
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
<?php
// [10]
    echo $stats->handleTwigTemplate(
        [
            'ws_top'    => $WS['TOP'],
            'ws_table'  => $WS['LOCTOP'],
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
