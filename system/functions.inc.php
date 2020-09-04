<?php
/**
 * Gets the system date correctly based on the default timezone.
 * Will not be the server time
 *
 * @deprecated Use the Date helper instead
 * @param bool $date_only Set to true to return only the date and not the time
 * @return string The current date and the time if the argument is false
 */
function getSystemDate($date_only = false) {
	if($date_only) return date("Y-m-d");
	return date("Y-m-d H:i:s");
}

/**
 * Gets the system date based if the system was GMT.
 * Will not be the server time
 *
 * @deprecated Use the Date helper instead
 * @param bool $date_only Set to true to return only the date and not the time
 * @return string The current date and the time if the argument is false in GMT
 */
function getSystemDateGMT($date_only = false) {
	if($date_only) return gmdate("Y-m-d");
	return gmdate("Y-m-d H:i:s");
}

/**
 *
 * @deprecated Use the Date helper instead
 * Enter description here ...
 * @param unknown_type $date
 */
function adjustTimeToLocalTime($date) {
	$time = strtotime($date);
	$offset = date("O");
	$hour = substr($offset, 0, 3);
	$mins = substr($offset, 3, 2);

	$time += (($hour * 60) + $mins) * 60;

	return date("Y-m-d H:i:s", $time);
}

/**
 * Gets the current URL as seen in the browsers navigation bar
 *
 * @return string The current URL
 */
function getCurrentURL() {
	$url = 'http';
	if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $url .= "s";

	$url .= "://";

	if($_SERVER["SERVER_PORT"] != "80")
	{
		$url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}
	else
	{
		$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $url;
}

/**
 * Gets the users ip address
 * @deprecated
 *
 * @return string The IP address of the user
 */
function getIPAddress() {
	if(!empty($_SERVER['HTTP_CLIENT_IP']))  {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

/**
 * Enter description here ...
 * @param unknown_type $email
 * @deprecated Use Validation_Helper->ValidEmail instead
 */
function validEmail($email) {
	return preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email);
}

/**
 * Generates a random password based on the passed parameters
 *
 * @param int $length The length that the password should be
 * @param int $sub_sets What type of characters should be in the password
 * @return string The generated password
 * @deprecated Use the Text Helper instead
 */
function random($length = 8, $sub_sets = RAN_ALL) {
	$alphabet = "";
	if($sub_sets & RAN_LOWER) $alphabet .= "abcdefghjklmnpqrstuvwxyz";
	if($sub_sets & RAN_UPPER) $alphabet .= "ABCDEFGHJKLMNPQRSTUVWXYZ";
	if($sub_sets & RAN_NUMBERS) $alphabet .= "0123456789";
	$word = "";
	for($i = 0; $i < $length; $i++)
	$word .= $alphabet[rand(0, strlen($alphabet) - 1)];

	return $word;
}

function humanDate($date, $date_only = false) {
	$raw = strtotime($date);
	$time = date("H:i", $raw);

	// Check today
	if(date("Y-m-d") == date("Y-m-d", $raw)) return $date_only ? "Today" : "Today at {$time}";

	// Check yesturday
	if(date("Y-m-d", strtotime("-1 days")) == date("Y-m-d", $raw)) return $date_only ? "Yesterday" : "Yesterday at {$time}";

	// Check last 7 days
	if(date("Y-m-d", strtotime("-6 days")) < date("Y-m-d", $raw)) return $date_only ? date("l", $raw) : date("l", $raw)." at {$time}";

	// Same year
	if(date("Y") == date("Y", $raw)) return $date_only ? date("j F", $raw) : date("j F", $raw)." at {$time}";

	// Else full date
	return $date_only ? date("j F Y", $raw) : date("j F Y", $raw)." at {$time}";
}

/**
 * CA Trew - 2010-02-13
 * Funtion that takes 2 date parameters and calcualtes the time difference between them.
 * Will convert those seconds into a human readable format
 *
 * @param string $first The begining date
 * @param string $last The end date
 * @return string
 */
function humanDateSpan($first, $last = null, $conjuction = false) {
	$first = strtotime($first);
	$last = $last ? strtotime($last) : time();
	if($first > $last) list($first, $last) = array($last, $first);

	$temp = $last - $first;
	$sec = $temp % 60; $temp = floor($temp / 60);
	$min = $temp % 60; $temp = floor($temp / 60);
	$hou = $temp % 24; $temp = floor($temp / 24);
	$day = $temp;

	$arr = array();
	if($day > 365) // 1 year and longer
	{
		$year = floor($day / 365);
		$arr[] = $year . " year".($year == 1 ? "" : "s");
		$mon = floor($day % 365 / 30);
		if($mon) $arr[] = $mon . " month".($mon == 1 ? "" : "s");
	}
	else if($day >= 14) // 2 weeks and longer
	{
		$week = floor($day / 7);
		$arr[] = $week . " week".($week == 1 ? "" : "s");
		$day = $day % 7;
		if($day) $arr[] = $day . " day".($day == 1 ? "" : "s");
	}
	else if($day >= 1)
	{
		$arr[] = $day . " day".($day == 1 ? "" : "s");
		if($day < 3 && $hou) $arr[] = $hou . " hour".($hou == 1 ? "" : "s");
	}
	else
	{
		if($hou) $arr[] = $hou . " hour".($hou == 1 ? "" : "s");
		if($min) $arr[] = $min . " minute".($min == 1 ? "" : "s");
		if($hou == 0 && $sec) $arr[] = $sec . " second".($sec == 1 ? "" : "s");
	}

	if($conjuction)
	{
		$return = "";
		$count = 0;
		foreach($arr as $item)
		{
			$count++;
			if($count == sizeof($arr) && $count > 1) $return .= " and ";
			if($count < sizeof($arr) && $count > 1) $return .= ", ";
			$return .= $item;
		}
		return $return;
	}

	return implode(" ", $arr);
}

function shortenTextPics($text, $length) {
	// Look for you tube video
	$pos = stripos($text, "<object");
	if($pos !== false)
	{
		$end = stripos($text, "</object>", $pos);
		$temp = substr($text, $pos, $end - $pos);
		if(stripos($temp, "youtube.com") !== false)
		{
			$text = trim(substr($text, 0, $pos))."YouTube Video. ".trim(substr($text, $end + 9));
		}
	}

	$content = str_replace(array("</p>", "<br />", "<li>", "<div>"), array(" </p>", " <br />", " <li>", " <div>"), $text);
	$content = trim(strip_tags($content, "<img>"));
	$ws = array(" ", "\t", "\n", "\r");

	$new = "";
	$pos = stripos($content, "<img");

	if($pos === false || $pos > $length) return shortenText($text, $length);
	global $config;
	$currentLength = 0;

	while($currentLength < $length)
	{
		$currentLength += strlen(substr($content, 0, $pos)) + 1;
		$new .= substr($content, 0, $pos). " ";
		$end = strpos($content, ">", $pos) + 1;
		$image = substr($content, $pos, $end - $pos);
		$content = trim(substr($content, $end));

		// Extract the key
		$key = substr($image, strpos($image, "ks_"));
		$key = substr($key, 0, strpos($key, "/"));
		$new .= "<img src=\"{$config['root_web']}client/knowledge-share/image/{$key}/?ajax=1&amp;thumb=1\" style=\"height: 50px; display: inline; position: relative; top: 4px\" /> ";
		$currentLength += 50;

		$pos = stripos($content, "<img");
		if($pos === false)
		{
			if($length - strlen($new) > 0)
			{
				$new .= shortenText($content, $length - strlen($new));
			}
			break;
		}
	}

	$content = str_replace($ws, " ", $new);
	while(strpos($content, "  ") !== false) $content = str_replace("  ", " ", $content);
	$content = trim($content);

	return trim($content);
}

function mysql_41_password($in) {
	$p = sha1($in, true);
	$p = sha1($p);
	return "*".strtoupper($p);
}

function simplifyCaptchaCode($code) {
	$code = strtoupper($code);
	return str_replace(array("0", "o", "O", "1", "l", "L", "Z"), array("0", "0", "0", "1", "1", "1", "2"), $code);
}

function cleanGetArray() {
	foreach($_GET as $key => $value)
	{
		$_GET[$key] = cleanParam($value, true);
	}
}

function cleanParam($value, $xss_clean = false) {
	$invisFind = array("/%0[0-8bcef]/", "/%1[0-9a-f]/",
			"/[\\x00-\\x08]/", "/\\x0b/", "/\\x0c/", "/[\\x0e-\\x1f]/");
	$neverAllowed = array(
			"document.cookie", "document.write", ".parentNode", ".innerHTML",
			"window.location", "-moz-binding", "<!--", "-->", "<![CDATA["
			);

			// Check for magic quotes
			if(get_magic_quotes_gpc())
			{
				$value = stripslashes($value);
			}

			// If no XSS clean then return
			if(!$xss_clean) return $value;

			// Remove invisible characters
			while(true)
			{
				$temp = $value;
				$value = preg_replace($invisFind, "", $value);
				if($temp == $value) break;
			}

			// Validate stand characters
			$value = preg_replace('#(&\#?[0-9a-z]{2,})([\x00-\x20])*;?#i', "\\1;\\2", $value);
			$value = preg_replace('#(&\#x?)([0-9A-F]+);?#i',"\\1\\2;", $value);

			// PHP url decode
			$value = rawurldecode($value);

			// Convert entities to ascii
			$value = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", "clean_convert_attribute", $value);
			$value = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", "clean_html_entity_decode_callback", $value);

			// Invis chars again
			while(true)
			{
				$temp = $value;
				$value = preg_replace($invisFind, "", $value);
				if($temp == $value) break;
			}

			// Tabs to spaces
			if(strpos($value, "\t") !== false) $value = str_replace("\t", "   ", $value);

			// Never allowed
			$value = str_ireplace($neverAllowed, "RM", $value);

			// No PHP
			$value = str_ireplace(array("<?php", '<?', '?'.'>'),  array('&lt;?php', '&lt;?', '?&gt;'), $value);

			// Get rid of scripts
			while(true)
			{
				$temp = $value;
				if(preg_match("/xss/i", $value) || preg_match("/script/i", $value))
				$value = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '[RM]', $value);
				if($value == $temp) break;
			}

			// Naught html
			$naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
			$value = preg_replace_callback('#<(/*\s*)('.$naughty.')([^><]*)([><]*)#is', "clean_sanitize_naughty_html", $value);

			// Naughty elements
			$value = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $value);

			// Never allowed
			$value = str_ireplace($neverAllowed, "RM", $value);

			return $value;
}

if(!function_exists('sys_get_temp_dir') ) {
	// Based on http://www.phpit.net/
	// Taken from php.net
	// article/creating-zip-tar-archives-dynamically-php/2/
	function sys_get_temp_dir()
	{
		// Try to get from environment variable
		if ( !empty($_ENV['TMP']) )
		{
			return realpath( $_ENV['TMP'] );
		}
		else if ( !empty($_ENV['TMPDIR']) )
		{
			return realpath( $_ENV['TMPDIR'] );
		}
		else if ( !empty($_ENV['TEMP']) )
		{
			return realpath( $_ENV['TEMP'] );
		}

		// Detect by creating a temporary file
		else
		{
			// Try to use system's temporary directory
			// as random name shouldn't exist
			$temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
			if ( $temp_file )
			{
				$temp_dir = realpath( dirname($temp_file) );
				unlink( $temp_file );
				return $temp_dir;
			}
			else
			{
				return FALSE;
			}
		}
	}
}

function pharmaceuticalMediumList()
{
	$list = array('Tablets','Syrup','Cream','Capsules','Nasal Spray','Honey and Lemon', 'Injection',
				'Nasal Drops','Lozenges', 'Powder for Injection', 'Elixir', 'Ampoule',
				'Throat Gargle','Throat Spray','Effervescent','Mixture', 'Suspension', 'Effervescent Tablets',
			'Solution for Intramuscular Injection',
			'Solution for IV Infusion', 'Powder', 'Vaginal Cream', 'Medicated Shampoo', 'Antiseptic Cream',
			'Antiseptic Ointment', 'Antiseptic Solution', 'First Aid Cream', 'Rapid Card',
			'Solution', 'Vaginal Gel', 'Gel', 'Sterile Solution', 'Granules', 'Powder and Solvent for Solution for Injection'
			);
			sort($list);
			return $list;
}

function clean_convert_attribute($match)
{
	return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
}

function clean_sanitize_naughty_html($matches)
{
	$str = '&lt;'.$matches[1].$matches[2].$matches[3];
	$str .= str_replace(array('>', '<'), array('&gt;', '&lt;'), $matches[4]);
	return $str;
}

function clean_html_entity_decode_callback($match)
{
	$charset = "UTF-8";
	$str = $match[0];
	if(stristr($str, '&') === false) return $str;
	if(function_exists('html_entity_decode')
	&& (strtolower($charset) != 'utf-8' || version_compare(phpversion(), '5.0.0', '>=')))
	{
		$str = html_entity_decode($str, ENT_COMPAT, $charset);
		$str = preg_replace('~&#x(0*[0-9a-f]{2,5})~ei', 'chr(hexdec("\\1"))', $str);
		return preg_replace('~&#([0-9]{2,4})~e', 'chr(\\1)', $str);
	}

	// Numeric Entities
	$str = preg_replace('~&#x(0*[0-9a-f]{2,5});{0,1}~ei', 'chr(hexdec("\\1"))', $str);
	$str = preg_replace('~&#([0-9]{2,4});{0,1}~e', 'chr(\\1)', $str);

	// Literal Entities - Slightly slow so we do another check
	if (stristr($str, '&') === false)
	{
		$str = strtr($str, array_flip(get_html_translation_table(HTML_ENTITIES)));
	}

	return $str;
}

function auto_version($file)
{
	if(strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'] . $file))
		return $file;

	$mtime = filemtime($_SERVER['DOCUMENT_ROOT'] . $file);
	return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
}

function countryCityFromIP($ipAddr = null)
{
	if(!$ipAddr) $ipAddr = getIPAddress();

	//function to find country and city from IP address
	//Developed by Roshan Bhattarai http://roshanbh.com.np

	//verify the IP address for the
	if(ip2long($ipAddr)== -1 || ip2long($ipAddr) === false) return null;
	$ipDetail=array(); //initialize a blank array

	$ipDetail['ip_address'] = $ipAddr;

	//get the XML result from hostip.info
	$xml = file_get_contents("http://api.hostip.info/?ip=".$ipAddr);

	//get the city name inside the node <gml:name> and </gml:name>
	$xml2 = substr($xml, strpos($xml, "<Hostip>"));
	preg_match("@name>(.*?)</gml@si",$xml2,$match);

	//assing the city name to the array
	$ipDetail['city']=$match[2];

	//get the country name inside the node <countryName> and </countryName>
	preg_match("@<countryName>(.*?)</countryName>@si",$xml,$matches);

	//assign the country name to the $ipDetail array
	$ipDetail['country']=$matches[1];

	//get the country name inside the node <countryName> and </countryName>
	preg_match("@<countryAbbrev>(.*?)</countryAbbrev>@si",$xml,$cc_match);
	$ipDetail['country_code']=$cc_match[1]; //assing the country code to array

	//return the array containing city, country and country code
	return $ipDetail;
}

//fixing the problem caused by using the depricated mime_content_type
if(!function_exists('mime_content_type')) {
	function mime_content_type($filename) {
		$mime_types = array(
					'txt' => 'text/plain',
					'htm' => 'text/html',
					'html' => 'text/html',
					'php' => 'text/html',
					'css' => 'text/css',
					'js' => 'application/javascript',
					'json' => 'application/json',
					'xml' => 'application/xml',
					'swf' => 'application/x-shockwave-flash',
					'flv' => 'video/x-flv',
					'epub'=> 'application/epub+zip',
		// images
					'png' => 'image/png',
					'jpe' => 'image/jpeg',
					'jpeg' => 'image/jpeg',
					'jpg' => 'image/jpeg',
					'gif' => 'image/gif',
					'bmp' => 'image/bmp',
					'ico' => 'image/vnd.microsoft.icon',
					'tiff' => 'image/tiff',
					'tif' => 'image/tiff',
					'svg' => 'image/svg+xml',
					'svgz' => 'image/svg+xml',
		// archives
					'zip' => 'application/zip',
					'rar' => 'application/x-rar-compressed',
					'exe' => 'application/x-msdownload',
					'msi' => 'application/x-msdownload',
					'cab' => 'application/vnd.ms-cab-compressed',
		// audio/video
					'mp3' => 'audio/mpeg',
					'qt' => 'video/quicktime',
					'mov' => 'video/quicktime',
		// adobe
					'pdf' => 'application/pdf',
					'psd' => 'image/vnd.adobe.photoshop',
					'ai' => 'application/postscript',
					'eps' => 'application/postscript',
					'ps' => 'application/postscript',
		// ms office
					'doc' => 'application/msword',
					'rtf' => 'application/rtf',
					'xls' => 'application/vnd.ms-excel',
					'ppt' => 'application/vnd.ms-powerpoint',
		// open office
					'odt' => 'application/vnd.oasis.opendocument.text',
					'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);
		$dot = '.';
		$explode = explode($dot,$filename);
		$ext = strtolower(array_pop($explode));
		if (array_key_exists($ext, $mime_types)) {
			return $mime_types[$ext];
		}
		elseif (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
		else {
			return 'application/octet-stream';
		}
	}

}

function startsWith($haystack, $needle) {
	// search backwards starting from haystack length characters from the end
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}


