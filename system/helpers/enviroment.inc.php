<?php
if(!defined("HELPER_ENVIROMENT_PHP"))
{
	define("HELPER_ENVIROMENT_PHP", 1);
	
	class ClientInformation
	{
		public $ipAddress;
		public $osName;
		public $osVersion;
		public $browserName;
		public $browserVersion;
		public $isBot;
	}
	
	class Enviroment_Helper
	{
		/**
		 * Gets the users ip address
		 *
		 * @return string The IP address of the user
		 */
		public function IPAddress()
		{
			if(!empty($_SERVER['HTTP_CLIENT_IP']))
		    {
		    	$ip = $_SERVER['HTTP_CLIENT_IP'];
		    }
		    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		    {
		    	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		    }
		    else
		    {
		    	$ip = $_SERVER['REMOTE_ADDR'];
		    }
		    return $ip;
		}
		
		/**
		 * @return ClientInformation
		 */
		public function ClientInformation()
		{
			$info = new ClientInformation();
			$info->ipAddress = $this->IPAddress();
			
	    	$useragent = $_SERVER['HTTP_USER_AGENT'];
	    	
	    	// Determine browser name, version and if they are a bot
			if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)) 
			{
				$info->browserName = "IE";
				$info->browserVersion = $matched[1];
				$info->isBot = false;
			} 
			elseif (preg_match( "|Opera/([0-9\.]+)|",$useragent,$matched)) 
			{
				$info->browserName = "Opera";
				$info->browserVersion = $matched[1];
				$info->isBot = false;
			} 
			elseif(preg_match("|Firefox/([0-9\.]+)|",$useragent,$matched)) 
			{
				$info->browserName = "Firefox";
				$info->browserVersion = $matched[1];
				$info->isBot = false;
			} 
			elseif(preg_match("|Chrome/([0-9\.]+)|",$useragent,$matched)) 
			{
				$info->browserName = "Chrome";
				$info->browserVersion = $matched[1];
				$info->isBot = false;
			} 
			elseif(preg_match("|Safari/([0-9\.]+)|",$useragent,$matched)) 
			{
				if(stristr($useragent, "mobile"))
				{
					$info->browserName = "Mobile Safari";
				}
				else
				{
					$info->browserName = "Safari";
				}
				$info->browserVersion = $matched[1];
				$info->isBot = false;
			}
			elseif(preg_match("|BlackBerry8520+/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "Blackberry 8520";
				$info->browserVersion = $matched[2];
				$info->isBot = false;
			}  
			elseif(preg_match("|BlackBerry9700+/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "Blackberry 9700";
				$info->browserVersion = $matched[2];
				$info->isBot = false;
			}  
			elseif(strstr($useragent,"AdsBot-Google")) 
			{
				$info->browserName = "Google Ads Bot";
				$info->browserVersion = "";
				$info->isBot = true;
			} 
			elseif(preg_match("|msnbot/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "MSN Bot";
				$info->browserVersion = $matched[1];
				$info->isBot = true;
			} 
			elseif(preg_match("|CPDCardScanner/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "CPD Card Scanner";
				$info->browserVersion = $matched[1];
				$info->isBot = true;
			} 
			elseif(preg_match("|Googlebot/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "Google Bot";
				$info->browserVersion = $matched[1];
				$info->isBot = true;
			} 
			elseif(strstr($useragent, "Yahoo! Slurp")) 
			{
				$info->browserName = "Yahoo Slurp Bot";
				$info->browserVersion = "";
				$info->isBot = true;
			} 
			elseif(preg_match("|Google Desktop ([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "Google Desktop";
				$info->browserVersion = $matched[1];
				$info->isBot = true;
			} 
			elseif(preg_match("|Gigabot/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "Giga Bot";
				$info->browserVersion = $matched[1];
				$info->isBot = true;
			} 
			elseif(preg_match("|MJ12bot/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "MJ12 Bot";
				$info->browserVersion = $matched[1];
				$info->isBot = true;
			} 
			elseif(preg_match("|YandexBot/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "Yandex Bot";
				$info->browserVersion = $matched[1];
				$info->isBot = true;
			} 
			elseif(preg_match("|Baiduspider/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "Baiduspider";
				$info->browserVersion = $matched[1];
				$info->isBot = true;
			} 
			elseif(preg_match("|Ezooms/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "Ezooms";
				$info->browserVersion = $matched[1];
				$info->isBot = true;
			} 
			elseif(preg_match("|DotBot/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "DotBot";
				$info->browserVersion = $matched[1];
				$info->isBot = true;
			} 
			elseif(preg_match("|bingbot/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "Bing Bot";
				$info->browserVersion = $matched[1];
				$info->isBot = true;
			} 
			elseif(preg_match("|findlinks/([0-9a-zA-Z\.]+)|", $useragent, $matched)) 
			{
				$info->browserName = "Findlinks";
				$info->browserVersion = $matched[1];
				$info->isBot = true;
			} 
			else 
			{
			    // browser not recognized!
				$info->browserName = $useragent;
				$info->browserVersion = "";
				$info->isBot = false;
			}
			
			// Determine OS
			$os_version = "";
			if(strstr($useragent, "Win")) 
			{
			    $info->osName = "Windows";
			    $info->osVersion = $this->OSVersion($useragent, $info->osName);
			} 
			elseif(strstr($useragent, "Mac")) 
			{
			    $info->osName = "Mac";
			    $info->osVersion = $this->OSVersion($useragent, $info->osName);
			} 
			elseif(stristr($useragent, "Android")) 
			{
			    $info->osName = "Android";
			    $info->osVersion = $this->OSVersion($useragent, $info->osName);
			} 
			elseif(strstr($useragent, "Linux")) 
			{
			    $info->osName = "Linux";
			    $info->osVersion = $this->OSVersion($useragent, $info->osName);
			} 
			elseif(strstr($useragent, "Unix")) 
			{
			    $info->osName = "Unix";
			    $info->osVersion = $this->OSVersion($useragent, $info->osName);
			} 
			elseif(strstr($useragent, "BlackBerry")) 
			{
			    $info->osName = "Blackberry";
			    $info->osVersion = $this->OSVersion($useragent, $info->osName);
			} 
			elseif(strstr($useragent, "AdsBot-Google") || strstr($useragent, "Googlebot")) 
			{
			    $info->osName = "Bot";
			    $info->osVersion = "";
			} 
			elseif(strstr($useragent, "msnbot")) 
			{
			    $info->osName = "Bot";
			    $info->osVersion = "";
			} 
			elseif(strstr($useragent, "Yahoo! Slurp")) 
			{
			    $info->osName = "Bot";
			    $info->osVersion = "";
			} 
			elseif(strstr($useragent, "Google Desktop")) 
			{
			    $info->osName = "Google Sidebar";
			    $info->osVersion = "";
			} 
			elseif(strstr($useragent, "Baiduspider")) 
			{
			    $info->osName = "Baiduspider";
			    $info->osVersion = "";
			} 
			elseif(strstr($useragent, "Gigabot")) 
			{
			    $info->osName = "Bot";
			    $info->osVersion = "";
			} 
			elseif(strstr($useragent, "Ezooms")) 
			{
			    $info->osName = "Ezooms";
			    $info->osVersion = "";
			} 
			elseif(strstr($useragent, "DotBot")) 
			{
			    $info->osName = "DotBot";
			    $info->osVersion = "";
			} 
			elseif(strstr($useragent, "bingbot")) 
			{
			    $info->osName = "Bing Bot";
			    $info->osVersion = "";
			} 
			elseif(strstr($useragent, "MJ12bot")) 
			{
			    $info->osName = "MJ12 Bot";
			    $info->osVersion = "";
			} 
			elseif(strstr($useragent, "YandexBot")) 
			{
			    $info->osName = "YandexBot";
			    $info->osVersion = "";
			} 
			elseif(strstr($useragent, "findlinks")) 
			{
			    $info->osName = "Findlinks";
			    $info->osVersion = "";
			} 
			else 
			{
			    $info->osName = $useragent;
			    $info->osVersion = "";
			}
			
			return $info;
		}
		
		protected function OSVersion($useragent, $os)
		{
			$OSList = array(
				'3.11' => 'Win16',
				'95' => '(Windows 95)|(Win95)|(Windows_95)',
				'98' => '(Windows 98)|(Win98)',
				'2000' => '(Windows NT 5.0)|(Windows 2000)',
				'XP' => '(Windows NT 5.1)|(Windows XP)',
				'Server 2003' => '(Windows NT 5.2)',
				'Vista' => '(Windows NT 6.0)',
				'7' => '(Windows NT 7.0)|(Windows NT 6.1)',
				'NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
				'ME' => 'Windows ME',
				'Open BSD' => 'OpenBSD',
				'Sun OS' => 'SunOS',
				'Linux' => '(Linux)|(X11)',
				'Mac OS' => '(Mac_PowerPC)|(Macintosh)',
				'QNX' => 'QNX',
				'BeOS' => 'BeOS',
				'OS/2' => 'OS/2'
			);
			
			if($os == "Android")
			{
				$OSList = array(
					'Froyo' => 'Froyo',
					'Gingerbread' => 'Gingerbread',
					'Honeycomb' => 'Honeycomb',
				);
			}
			
			foreach($OSList as $CurrOS => $Match)
			{
				// Find a match
				if (preg_match("/{$Match}/i", $useragent))
				{
					return $CurrOS;
				}
			}
			return "";
		}
	}
}
?>