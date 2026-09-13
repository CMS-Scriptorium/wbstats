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

defined('WB_PATH') OR die(header('Location: ../index.php'));

class Counter extends Config
{
	private $ip;
	private $referer = '';
	private $host = '';
	private $referer_host = '';
	private $referer_spam= 0;
	private $page;
	private $keywords = '';
	private $language = '';
	private $agent = '';
	private $browser = '';
	private $browser_version = '';
	private $os = '';
	private $location = '';
	private $response_code;
	private $session;
	private $utm = array("source" => '',"medium" => '',"campaign" => '',"term" => '',"content" => '',);
	
	private $time;
	private $day;
	private $month;
	private $old_data;
	private $old_date;
	private $reload;
	private $online;
	
    // Internal use 
    protected array $lowerBrowser = [];

	public function __construct() {
		$this->init();
		$this->count();
	}

	public function init() {
		global $database;
		$time = time();
		$this->time = $time;
		$this->day   = date("Ymd",$time);
		$this->month = date("Ym",$time);

		$oNOW = new \DateTime();
		$oNOW->modify("-90 day"); // 90 days ago today
		$this->old_data = $oNOW->getTimestamp(); 
		$this->old_date = date("Ymd", $this->old_data);
//		$this->old_data = strtotime(date("Ymd", mktime(0, 0, 0, date("n"), date("j") - 90, date("Y")))); // 90 days
//		$this->old_date = date("Ymd", mktime(0, 0, 0, date("n"), date("j") - 90, date("Y"))); // 90 days
		$this->reload = 3 * 60 * 60 ;
		$this->online = $time - 3 * 60;
		
		// make sure a visitor only once runs the cleanup!
		if(!isset($_SESSION['cleanstats'])) {
			$_SESSION['cleanstats'] = 'done';
            $database->query("DELETE FROM " . self::TABLE_IPS . " WHERE `time` < '" . $this->old_data . "'");
            $database->query("DELETE FROM " . self::TABLE_PAGES . " WHERE `day` < '" . $this->old_date . "'");
            $database->query("DELETE FROM " . self::TABLE_REF . " WHERE `day` < '" . $this->old_date . "'");
            $database->query("DELETE FROM " . self::TABLE_KEY . " WHERE `day` < '" . $this->old_date . "'");
            $database->query("DELETE FROM " . self::TABLE_LANG . " WHERE `day` < '" . $this->old_date . "'");
            $database->query("DELETE FROM " . self::TABLE_BROWSER . " WHERE `day` < '" . $this->old_date . "'");
            $database->query("DELETE FROM " . self::TABLE_HIST . " WHERE `timestamp` < '" . $this->old_data . "'");
            $database->query("DELETE FROM " . self::TABLE_LOC . " WHERE `timestamp` < '" . $this->old_data . "'");
            $database->query("DELETE FROM " . self::TABLE_UTM . " WHERE `timestamp` < '" . $this->old_data . "'");
        }
		$id = $database->get_one("SELECT `id` FROM " . self::TABLE_DAY . " WHERE `day` = '" . $this->day . "'");
        if (!$id)
        {
            $database->query("INSERT INTO " . self::TABLE_DAY . " (day, user, view) values ('" . $this->day . "', '0', '0')");
        }
    }

	public function count() {
		global $database;
		$this->getHosts();
		$this->getKeywords();
		$this->getSearch();
		$this->getUTM();
		
        if ($this->newUser())
        {
			if ($this->referer_host && stristr($this->host, $this->referer_host) === false)
            {
                if (!$id = $database->get_one("SELECT `id` from `" . self::TABLE_REF . "` WHERE `referer`='" . $this->referer_host . "' AND day='" . $this->day . "'"))
                {
                    $database->query("INSERT INTO `" . self::TABLE_REF . "` (`day`, `referer`, `view`, `spam`) VALUES ('" . $this->day . "', '" . $this->referer_host . "', '1','" . $this->referer_spam . "' )");
                } else
                {
                    $database->query("UPDATE `" . self::TABLE_REF . "` SET `view`=`view`+1 where `id`=" . $id);
                }
            }
            
            if ($this->language)
            {
                if (!$id = $database->get_one("SELECT `id` from `" . self::TABLE_LANG . "` WHERE `language`='" . $this->language . "' AND `day`='" . $this->day . "'"))
                {
                    $database->query("INSERT INTO `" . self::TABLE_LANG . "` (`day`, `language`, `view`) VALUES ('" . $this->day . "', '" . $this->language . "', '1')");
                } else
                {
                    $database->query("UPDATE `" . self::TABLE_LANG . "` SET `view`=`view`+1 where `id`=" . $id);
                }
            }
            
            if ($this->agent)
            {
                if (!$id = $database->get_one("SELECT `id` from `" . self::TABLE_BROWSER . "` WHERE `agent`='" . $this->agent . "' AND `day`='" . $this->day . "'"))
                {
                    $database->query("INSERT INTO `" . self::TABLE_BROWSER . "` (`day`, `agent`, `os`, `browser`, `version`, `view`) 
					VALUES ('" . $this->day . "', '" . $this->agent . "', '" . $this->os . "', '" . $this->browser . "', '" . $this->browser_version . "', '1')");
                } else
                {
                    $database->query("UPDATE `" . self::TABLE_BROWSER . "` SET `view`=`view`+1 where `id`=" . $id);
                }
            }
        } 
		
		if ($this->keywords)
        {
            if (!$id = $database->get_one("SELECT `id` from `" . self::TABLE_KEY . "` WHERE `keyword`='" . $this->keywords . "' AND `day`='" . $this->day . "'"))
            {
                $database->query("INSERT INTO `" . self::TABLE_KEY . "` (`day`, `keyword`, `view`) VALUES ('" . $this->day . "', '" . $this->keywords . "', '1')");
            } else
            {
                $database->query("UPDATE `" . self::TABLE_KEY . "` SET `view`=`view`+1 where `id`=" . $id);
            }
        }

        if ($this->page <> "")
        {
            if (!$id = $database->get_one("SELECT `id` from `" . self::TABLE_PAGES . "` WHERE `page`='" . $this->page . "' AND `day`='" . $this->day . "'"))
            {
                $database->query("INSERT INTO `" . self::TABLE_PAGES . "` (`day`, `page`, `view`) VALUES ('" . $this->day . "', '" . $this->page . "', '1')");
            } else
            {
                $database->query("UPDATE `" . self::TABLE_PAGES . "` SET `view`=`view`+1 WHERE id='$id'");
            }
            $database->query("INSERT INTO `" . self::TABLE_HIST . "` (`timestamp`, `page`, `ip`,`session`,`status`) VALUES ('" . time() . "', '" . $this->page . "', '" . $this->ip . "', '" . $this->session . "', '" . $this->response_code . "')");

            if ($id = $database->get_one("SELECT `id` from `" . self::TABLE_UTM . "` WHERE `ip`='" . $this->ip . "' AND `session`='" . $this->session . "' AND `day`='" . $this->day . "'"))
            {
                $database->query("UPDATE `" . self::TABLE_UTM . "` SET `pagecount`=`pagecount`+1 WHERE id='$id'");
                if ($this->utm['source'])
                {
                    $this->utm['source'] = ''; // count campaign only once. i.e. page refresh
                }
            }

            if ($this->utm['source'])
            {
                $p = parse_url($this->page, PHP_URL_PATH);
                $database->query("INSERT INTO `" . self::TABLE_UTM . "` 
					(`timestamp`, `ip`, `campaign`, `source`,`medium`,`term`,`content`,`referer`,`day`,`page`,`session`,`pagecount`) 
					VALUES ('" . time() . "', '" . $this->ip . "', '" . $this->utm['campaign'] . "', '" . $this->utm['source'] . "', '" . $this->utm['medium'] . "', '" . $this->utm['term'] . "', '" . $this->utm['content'] . "', '" . $this->referer_host . "', '" . $this->day . "', '" . $p . "', '" . $this->session . "','1')");
            }
        }
    }

	public function getHosts() {
		global $referer;
		$fp = $this->getRealUserIp(); //. session_id(); 
		if(isset($_SERVER['HTTP_USER_AGENT'])) $fp .= $_SERVER['HTTP_USER_AGENT'];
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $fp .= $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$this->ip = md5($fp); 
        if (defined( 'ORG_REFERER' )) {
            $this->referer = ORG_REFERER;
		} elseif (isset($referer)) {
			$this->referer = $referer;
		} else {
			if(isset($_SERVER['HTTP_REFERER'])) $this->referer = $_SERVER['HTTP_REFERER'];
		} 	
		$this->page = $_SERVER['REQUEST_URI']; 
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$this->language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
		}
		$this->response_code = http_response_code(); // detect 404
		$this->host=$_SERVER["HTTP_HOST"]; 
		if (substr($this->host,0,4) == "www.") $this->host=substr($this->host,4);
		if($this->referer) {
			$this->referer_host = parse_url($this->referer, PHP_URL_HOST); // Referrer Host
			if(!$this->referer_host) $this->referer_host = '';
			if (substr($this->referer_host,0,4) == "www.") $this->referer_host=substr($this->referer_host,4);
		}
		$this->referer = $this->escapeString($this->referer);
		$this->page = $this->escapeString($this->page);
		$this->language = $this->escapeString($this->language);
		$this->referer_host = $this->escapeString($this->referer_host);
		$this->agent = '';
		if(isset($_SERVER['HTTP_USER_AGENT'])) {
			$res = $this->parse_user_agent();
			$this->agent = $this->escapeString($_SERVER['HTTP_USER_AGENT']);
			$this->os = $res['platform']; // .' '.$res['platform_version'];
			$this->browser = $res['browser'];
			$this->browser_version = $res['version'];
			/*
			echo '<!-- ';
			print_r($res);
			print_r($this->agent);
			echo ' -->';
			*/
		}
	}
	
	public function getRealUserIp(){
		$ip = '';
		switch(true){
			case (!empty($_SERVER['HTTP_X_REAL_IP'])) : $ip = $_SERVER['HTTP_X_REAL_IP']; break;
			case (!empty($_SERVER['HTTP_CLIENT_IP'])) : $ip = $_SERVER['HTTP_CLIENT_IP']; break;
			case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; break;
			default : $ip = $_SERVER['REMOTE_ADDR'];
		}
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			return $ip;
		}
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return $ip;
		}
		return '0.0.0.0';
	}
	
	public function getKeywords () {
		if($ref = parse_url($this->referer, PHP_URL_QUERY)) {
			parse_str( $ref, $parms );
			if(isset($parms['q']) && $parms['q']!="") $this->keywords = urldecode($parms['q']); 
			//elseif(isset($parms['q'])) 		$this->keywords = 'Searchkey not provided'; 
			elseif(isset($parms['p'])) 		$this->keywords = urldecode($parms['p']); 
			elseif(isset($parms['query'])) 	$this->keywords = urldecode($parms['query']); 
			$this->keywords = $this->escapeString($this->keywords);
		}
	}

	public function getSearch () {
		if($ref = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY)) {
			parse_str( $ref, $parms );
			if(isset($parms['string']))  {
				$this->keywords = "Local search: ".urldecode($parms['string']); 
				$this->keywords = $this->escapeString($this->keywords);
			}
		}
	}
		
	public function getUTM () {
		if ($ref = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY))
        {
			$p = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

			parse_str($ref, $parms);

            if (isset($parms['fbclid']))
            {
                $this->utm['campaign'] = "Facebook external link";
                $this->utm['source'] = "Facebook";
                $this->utm['medium'] = "External";
                $this->utm['content'] = "FBCLID - " . $p;
            }
            
            if (isset($parms['gclid']))
            {
                $this->utm['campaign'] = "Google external link";
                $this->utm['source'] = "Google";
                $this->utm['medium'] = "External / Advertisement";
                $this->utm['content'] = "GCLID - " . $p;
            }

            if (isset($parms['gbraid']))
            {
                $this->utm['campaign'] = "Google advertisement IOS (app)";
                $this->utm['source'] = "Google";
                $this->utm['medium'] = "External / Advertisement";
                $this->utm['content'] = "GBRAID - " . $p;
            }

            if (isset($parms['wbraid']))
            {
                $this->utm['campaign'] = "Google advertisement IOS (web)";
                $this->utm['source'] = "Google";
                $this->utm['medium'] = "External / Advertisement";
                $this->utm['content'] = "WBRAID - " . $p;
            }

            if (isset($parms['utm_campaign']))
            {
                $this->utm['campaign'] = $this->escapeString(urldecode($parms['utm_campaign']));
            }

            if (isset($parms['utm_source']))
            {
                $this->utm['source'] = $this->escapeString(urldecode($parms['utm_source']));
            }

            if (isset($parms['utm_medium']))
            {
                $this->utm['medium'] = $this->escapeString(urldecode($parms['utm_medium']));
            }

            if (isset($parms['utm_term']))
            {
                $this->utm['term'] = $this->escapeString(urldecode($parms['utm_term']));
            }

            if (isset($parms['utm_content']))
            {
                $this->utm['content'] = $this->escapeString(urldecode($parms['utm_content']));
            }

            if (!$this->utm['campaign'])
            {
                $this->utm['campaign'] = $this->utm['source'];
            }

            if (!$this->utm['content'])
            {
                $this->utm['content'] = $this->utm['source'] . " - No content";
            }
        }
    }
	

	public function newUser() {
		global $database, $table_day, $table_ips;
		$this->session = session_id();
		$timeout = time() - $this->reload;
		$loggedin = isset($_SESSION['USER_ID']) ? ", `loggedin`='1'":"";
		if($this->isIgnored()) {
			$this->page = ''; //prevent pagecounting
			return false;
		} elseif($this->isBot()) {
			$database->query("UPDATE ".self::TABLE_DAY." SET `bots`=`bots`+1 WHERE `day`='".$this->day."'");
			$this->page = ''; //prevent pagecounting
			return false;		
		} elseif($this->isSuspected()) {
			$database->query("UPDATE ".self::TABLE_DAY." SET `suspected`=`suspected`+1 WHERE `day`='".$this->day."'");
			$this->page = ''; //prevent pagecounting
			return false;
		} elseif($this->isRefererSpam()) {
			$database->query("UPDATE ".self::TABLE_DAY." SET `refspam`=`refspam`+1 WHERE `day`='".$this->day."'");
			$this->page = ''; //prevent pagecounting
			$this->keywords = ''; //prevent pagecounting
			$this->language = ''; //prevent pagecounting
			return true;
		} elseif(!$id = $database->get_one("SELECT `id` FROM ".self::TABLE_IPS." WHERE `ip`='".$this->ip."' AND `session`='".$this->session."' AND `time` > '$timeout' ORDER BY `id` DESC LIMIT 1")) {
			$city = $this->getCountryCode();
			$country = $this->getCountryCode(true);
			$database->query("INSERT INTO ".self::TABLE_IPS." (`ip`,`session`, `location`, `country`, `time`, `online`,`page`,`last_page`,`pages`,`language`,`os`,`browser`,`referer`,`ua`) VALUES 
				('".$this->ip."', '".$this->session."', '".$city."','".$country."', '".$this->time."', '".$this->time."', '".$this->page."', '".$this->page."','1','".$this->language."', '".$this->os."', '".$this->browser." (".$this->browser_version.")','".$this->referer_host."','".$this->agent."')");
			$database->query("UPDATE ".self::TABLE_DAY." SET `user`=`user`+1, `view`=`view`+1 WHERE `day`='".$this->day."'");
			return true;
		} else {
			$database->query("UPDATE ".self::TABLE_IPS." SET `online`='".$this->time."', `last_page`='".$this->page."', `pages`=`pages`+1, `last_status`='".$this->response_code."' $loggedin WHERE `id`='$id'");
			$database->query("UPDATE ".self::TABLE_DAY." SET `view`=`view`+1 WHERE `day`='".$this->day."'");
			return false;
		}
	}
	
	public function getCountryCode($countryOnly = false) {
		global $database, $table_loc;
		$ip = $this->getRealUserIp(); 
		$ipkey = md5($ip);
		$timeout = time() - $this->reload;
		$field = $countryOnly ? 'country':'location';
		
		if(!$location = $database->get_one("SELECT `$field` FROM ".self::TABLE_LOC." WHERE `ip`='".$ipkey."' and `location` != '' and `timestamp` > '$timeout' ORDER BY `timestamp` DESC LIMIT 1")) {
			if($ipdata = json_decode($this->getUrlContent('http://ip-api.com/json/'.$ip),true)) {
				
				if(!isset($ipdata['city']) || !$ipdata['city'])  				$ipdata['city'] = '- unknown -';
				if(!isset($ipdata['countryCode']) || !$ipdata['countryCode'])  	$ipdata['countryCode'] = '';
				if(!isset($ipdata['country']) || !$ipdata['country'])  			$ipdata['country'] = '';
				if(!isset($ipdata['lat']) || !$ipdata['lat'])  					$ipdata['lat'] = '';
				if(!isset($ipdata['lon']) || !$ipdata['lon'])  					$ipdata['lon'] = '';
				if(!isset($ipdata['timezone']) || !$ipdata['timezone'])  		$ipdata['timezone'] = '';

				$lat 			= $database->escapeString($ipdata['lat']);
				$lon 			= $database->escapeString($ipdata['lon']);
				$tz 			= $database->escapeString($ipdata['timezone']);
				$country 		= $database->escapeString($ipdata['country']);
				$country 		= str_ireplace("The ","",$country);  // "Netherlands" is sometimes "The Netherlands"
				$country_code 	= $database->escapeString($ipdata['countryCode']);
				$city 			= $database->escapeString($ipdata['city']);
				
				$location 		= $city;
				if($country_code) $location = $city.' ('.$country_code.')';

				$database->query("INSERT INTO ".self::TABLE_LOC." (`ip`,`location`,`timestamp`,`city`,`country`,`country_code`,`latitude`,`longitude`,`timezone`) 
					VALUES ('".$ipkey."','".$location."','".time()."','".$city."','".$country."','".$country_code."','".$lat."','".$lon."','".$tz."') ");
				if($countryOnly) $location = $country;
			}
		} else {
			// $city .= ' *';
		}
		return $location;
	}	
	
	public function noLongerFree_getCountryCode() {
		global $database, $table_loc;
		$ip = $this->getRealUserIp(); 
		$ipkey = md5($ip);
		if(!$city = $database->get_one("SELECT `location` FROM ".self::TABLE_LOC." WHERE `ip`='".$ipkey."' LIMIT 1")) {
			if($ipdata = unserialize($this->getUrlContent('http://www.geoplugin.net/php.gp?ip='.$ip))) {
				if(!$ipdata['geoplugin_city'])  $ipdata['geoplugin_city'] = '- unknown -';
				if(!$ipdata['geoplugin_countryCode'])  $ipdata['geoplugin_countryCode'] = '';

				$country_code 	= $ipdata['geoplugin_countryCode'];
				$city 			= $ipdata['geoplugin_city'];
				if($country_code) $city = $city.' ('.$country_code.')';
				$database->query("INSERT INTO ".self::TABLE_LOC." (`ip`,`location`,`timestamp`) VALUES ('".$ipkey."','".$city."','".time()."') ");
			}
		} else {
			// $city .= ' *';
		}
		return $city;
	}	
	
	public function getUrlContent($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'WBStats geoplugin');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		//curl_close($ch);
		return ($httpcode>=200 && $httpcode<300) ? $data : false;
	}	
	
	public function isBot(): bool
    {
        if (!isset($_SERVER['HTTP_USER_AGENT']))
        {
            return true;
        }

        require dirname(__DIR__) . '/botlist.php';

        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

        if (empty($userAgent))
        {
            return true; //Empty useraget is mostly a bot
        }

        foreach ($botUserAgents as $botUserAgent)
        {
            if (stripos($userAgent, $botUserAgent) !== false)
            {
                return true;
            }
        }
        return false;
    }

    public function isSuspected(): bool
    {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        {
            return true; // assuming no language means no human browser
        }
        if ($_SERVER['HTTP_ACCEPT_LANGUAGE'] == "*")
        {
            return true; // assuming no language means no human browser
        }
        // if($this->referer_host == parse_url(WB_URL, PHP_URL_HOST)) return true; // referer same as website domain
        return false;
    }

    public function isRefererSpam(): bool
    {
        if (!$this->referer_host)
        {
            return false;
        }
        
        require (dirname(__DIR__) . '/referers.php');
        
        foreach ($spamReferers as $spammer)
        {
            if (stripos($this->referer_host, $spammer) !== false)
            {
                $this->referer_spam = '1';
                return true;
            }
        }
        return false;
    }

    public function isIgnored() {
		global $database, $table_ips;
		$ip = $this->getRealUserIp(); // $_SERVER['REMOTE_ADDR'];
		$ip = $this->escapeString($ip);
		$r = $database->get_one("SELECT `ip` from `" . self::TABLE_IPS . "` WHERE `ip` = '" . $ip . "' AND `session`='ignore'");
		return $r == $ip;		
	}

	public function escapeString($string) {	
		global $database;
		if(!is_string($string)) return $string;  // make sure the parameter is a string
		if(is_object($database->DbHandle)) { 
			$rval = $database->escapeString($string);
		} else {
			$rval = mysql_real_escape_string ($string);
		}
		return $rval;
	}

	/**
	 * Parses a user agent string into its important parts
	 *
	 * @param string|null $u_agent User agent string to parse or null. Uses $_SERVER['HTTP_USER_AGENT'] on NULL
	 * @return string[] an array with 'browser', 'version' and 'platform' keys
	 * @throws \InvalidArgumentException on not having a proper user agent to parse.
	 */
	public function parse_user_agent( $u_agent = null ) {
		if( $u_agent === null && isset($_SERVER['HTTP_USER_AGENT']) ) {
			$u_agent = (string)$_SERVER['HTTP_USER_AGENT'];
		}

		if( $u_agent === null ) {
			throw new \InvalidArgumentException('parse_user_agent requires a user agent');
		}

		$platform = null;
		$browser  = null;
		$version  = null;

		$empty = array( self::PLATFORM => $platform, self::BROWSER => $browser, self::BROWSER_VERSION => $version );

		if( !$u_agent ) {
			return $empty;
		}

		if( preg_match('/\((.*?)\)/m', $u_agent, $parent_matches) ) {
			preg_match_all(<<<'REGEX'
/(?P<platform>BB\d+;|Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|(Open|Net|Free)BSD|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|X11|(New\ )?Nintendo\ (WiiU?|3?DS|Switch)|Xbox(\ One)?)
(?:\ [^;]*)?
(?:;|$)/imx
REGEX
				, $parent_matches[1], $result);

			$priority = array( 'Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android', 'FreeBSD', 'NetBSD', 'OpenBSD', 'CrOS', 'X11' );

			$result[self::PLATFORM] = array_unique($result[self::PLATFORM]);
			if( count($result[self::PLATFORM]) > 1 ) {
				if( $keys = array_intersect($priority, $result[self::PLATFORM]) ) {
					$platform = reset($keys);
				} else {
					$platform = $result[self::PLATFORM][0];
				}
			} elseif( isset($result[self::PLATFORM][0]) ) {
				$platform = $result[self::PLATFORM][0];
			}
		}

		if( $platform == 'linux-gnu' || $platform == 'X11' ) {
			$platform = 'Linux';
		} elseif( $platform == 'CrOS' ) {
			$platform = 'Chrome OS';
		}

		preg_match_all(<<<'REGEX'
%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Iceweasel|IceCat|Safari|MSIE|Trident|AppleWebKit|
TizenBrowser|(?:Headless)?Chrome|YaBrowser|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|Edg|CriOS|UCBrowser|Puffin|OculusBrowser|SamsungBrowser|
Baiduspider|Applebot|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|
Valve\ Steam\ Tenfoot|
NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
(?:\)?;?)
(?:(?:[:/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix
REGEX
			, $u_agent, $result);

		// If nothing matched, return null (to avoid undefined index errors)
		if( !isset($result[self::BROWSER][0]) || !isset($result[self::BROWSER_VERSION][0]) ) {
			if( preg_match('%^(?!Mozilla)(?P<browser>[A-Z0-9\-]+)(/(?P<version>[0-9A-Z.]+))?%ix', $u_agent, $result) ) {
				return array( self::PLATFORM => $platform ?: null, self::BROWSER => $result[self::BROWSER], self::BROWSER_VERSION => empty($result[self::BROWSER_VERSION]) ? null : $result[self::BROWSER_VERSION] );
			}

			return $empty;
		}

		if( preg_match('/rv:(?P<version>[0-9A-Z.]+)/i', $u_agent, $rv_result) ) {
			$rv_result = $rv_result[self::BROWSER_VERSION];
		}

		$browser = $result[self::BROWSER][0];
		$version = $result[self::BROWSER_VERSION][0];

		$this->lowerBrowser = array_map('strtolower', $result[self::BROWSER]);

		$key = 0;
		$val = '';
		if ($this->browserFindT(
            [
                'OPR'       => 'Opera',
                'UCBrowser' => 'UC Browser',
                'YaBrowser' => 'Yandex',
                'Iceweasel' => 'Firefox',
                'Icecat'    => 'Firefox',
                'CriOS'     => 'Chrome',
                'Edg'       => 'Edge'
            ],
            $key,
            $browser)
        ){
			$version = $result[self::BROWSER_VERSION][$key];
		} elseif( $this->browserFind('Playstation Vita', $key, $platform) ) {
			$platform = 'PlayStation Vita';
			$browser  = 'Browser';
		} elseif( $this->browserFind(array( 'Kindle Fire', 'Silk' ), $key, $val) ) {
			$browser  = $val == 'Silk' ? 'Silk' : 'Kindle';
			$platform = 'Kindle Fire';
			if( !($version = $result[self::BROWSER_VERSION][$key]) || !is_numeric($version[0]) ) {
				$version = $result[self::BROWSER_VERSION][array_search('Version', $result[self::BROWSER])];
			}
		} elseif( $this->browserFind('NintendoBrowser', $key) || $platform == 'Nintendo 3DS' ) {
			$browser = 'NintendoBrowser';
			$version = $result[self::BROWSER_VERSION][$key];
		} elseif( $this->browserFind('Kindle', $key, $platform) ) {
			$browser = $result[self::BROWSER][$key];
			$version = $result[self::BROWSER_VERSION][$key];
		} elseif( $this->browserFind('Opera', $key, $browser) ) {
			$this->browserFind('Version', $key);
			$version = $result[self::BROWSER_VERSION][$key];
		} elseif( $this->browserFind('Puffin', $key, $browser) ) {
			$version = $result[self::BROWSER_VERSION][$key];
			if( strlen($version) > 3 ) {
				$part = substr($version, -2);
				if( ctype_upper($part) ) {
					$version = substr($version, 0, -2);

					$flags = array( 'IP' => 'iPhone', 'IT' => 'iPad', 'AP' => 'Android', 'AT' => 'Android', 'WP' => 'Windows Phone', 'WT' => 'Windows' );
					if( isset($flags[$part]) ) {
						$platform = $flags[$part];
					}
				}
			}
		} elseif( $this->browserFind(array( 'Applebot', 'IEMobile', 'Edge', 'Midori', 'Vivaldi', 'OculusBrowser', 'SamsungBrowser', 'Valve Steam Tenfoot', 'Chrome', 'HeadlessChrome' ), $key, $browser) ) {
			$version = $result[self::BROWSER_VERSION][$key];
		} elseif( $rv_result && $this->browserFind('Trident') ) {
			$browser = 'MSIE';
			$version = $rv_result;
		} elseif( $browser == 'AppleWebKit' ) {
			if( $platform == 'Android' ) {
				$browser = 'Android Browser';
			} elseif($platform && strpos($platform, 'BB') === 0 ) {
				$browser  = 'BlackBerry Browser';
				$platform = 'BlackBerry';
			} elseif( $platform == 'BlackBerry' || $platform == 'PlayBook' ) {
				$browser = 'BlackBerry Browser';
			} else {
				$this->browserFind('Safari', $key, $browser) || $this->browserFind('TizenBrowser', $key, $browser);
			}

			$this->browserFind('Version', $key);
			$version = $result[self::BROWSER_VERSION][$key];
		} elseif( $pKey = preg_grep('/playstation \d/i', $result[self::BROWSER]) ) {
			$pKey = reset($pKey);

			$platform = 'PlayStation ' . preg_replace('/\D/', '', $pKey);
			$browser  = 'NetFront';
		}
		$version = intval($version);
		return array( self::PLATFORM => $platform ?: null, self::BROWSER => $browser ?: null, self::BROWSER_VERSION => $version ?: null );
	}

    protected function browserFind(mixed $searchOrg, mixed &$key = null, mixed &$value = null): bool
    {
        $search = is_array($searchOrg) ? $searchOrg : [$searchOrg];

        foreach ($search as $val)
        {
            $xkey = array_search(strtolower($val), $this->lowerBrowser);
            if ($xkey !== false)
            {
                $value = $val;
                $key = $xkey;

                return true;
            }
        }

        return false;
    }

    protected function browserFindT(array $search, &$key = null, &$value = null): bool
    {
        $value2 = null;
        if ($this->browserFind(array_keys($search), $key, $value2))
        {
            $value = $search[$value2];

            return true;
        }

        return false;
    }
}
