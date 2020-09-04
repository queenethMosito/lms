<?php
if(!defined("HELPER_INPUT_PHP"))
{
	define("HELPER_INPUT_PHP", 1);
	
	/**
	 * Class to help clean up post/get/cookie and server variables
	 * Performs a simple clean and optionally a cross server script (xss) cleanup
	 *
	 */
	class Input_Helper 
	{
		/**
		 * Fetch and clean a GET variable
		 *
		 * @param string $key
		 * @param bool $xss_clean
		 * @return unknown
		 */
		public function get($key, $xss_clean = false)
		{
			if(!isset($_GET[$key])) return false;
			return $this->_clean($_GET[$key], $xss_clean);
		}
		
		/**
		 * Fetch and clean a POST variable
		 *
		 * @param string $key
		 * @param bool $xss_clean
		 * @return unknown
		 */
		public function post($key, $xss_clean = false)
		{
			if(!isset($_POST[$key])) return false;
			return $this->_clean($_POST[$key], $xss_clean);
		}
		
		/**
		 * Fetch and clean a COOKIE variable
		 *
		 * @param string $key
		 * @param bool $xss_clean
		 * @return unknown
		 */
		public function cookie($key, $xss_clean = false)
		{
			if(!isset($_COOKIE[$key])) return false;
			return $this->_clean($_COOKIE[$key], $xss_clean);
		}
		
		/**
		 * Fetch and clean a SERVER variable
		 *
		 * @param string $key
		 * @param bool $xss_clean
		 * @return unknown
		 */
		public function server($key, $xss_clean = false)
		{
			if(!isset($_SERVER[$key])) return false;
			return $this->_clean($_SERVER[$key], $xss_clean);
		}
		
		/**
		 * Fetch and clean a GET variable. If there is no GET then fetch the POST
		 *
		 * @param string $key
		 * @param bool $xss_clean
		 * @return unknown
		 */
		public function get_post($key, $xss_clean = false)
		{
			if(isset($_GET[$key])) return $this->get($key, $xss_clean);
			else return $this->post($key, $xss_clean);
		}
		
		/**
		 * Fetch and clean a POST variable. If there is no POST then fetch the GET
		 *
		 * @param string $key
		 * @param bool $xss_clean
		 * @return unknown
		 */
		public function post_get($key, $xss_clean = false)
		{
			if(isset($_POST[$key])) return $this->post($key, $xss_clean);
			else return $this->get($key, $xss_clean);
		}
		
		/**
		 * Cleanup the variable
		 *
		 * @param unknown $value
		 * @param bool $xss_clean
		 * @return unknown
		 */
		protected function _clean($value, $xss_clean = false)
		{
			// Check if the value is an array
			if(is_array($value))
			{
				$data = array();
				foreach($value as $key => $value2)
				{
					$data[$key] = $this->_clean($value2, $xss_clean);
				}
				return $data;
			}
			
			// Check for magic quotes
	        if(get_magic_quotes_gpc()) 
	        { 
	        	$value = stripslashes($value); 
	        }
	        
	        // If no XSS clean then return
			if(!$xss_clean) return $value;
			
			// Prepare some variables
			$invisFind = array("/%0[0-8bcef]/", "/%1[0-9a-f]/", 
				"/[\\x00-\\x08]/", "/\\x0b/", "/\\x0c/", "/[\\x0e-\\x1f]/");
			$neverAllowed = array(
				"document.cookie", "document.write", ".parentNode", ".innerHTML",
				"window.location", "-moz-binding", "<!--", "-->", "<![CDATA["
			);
				
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
			$value = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", array($this, "_clean_convert_attribute"), $value);
			$value = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", array($this, "_clean_html_entity_decode_callback"), $value);
			
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
			$value = preg_replace_callback('#<(/*\s*)('.$naughty.')([^><]*)([><]*)#is', array($this, "_clean_sanitize_naughty_html"), $value);
			
			// Naughty elements
			$value = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $value);
			
			// Never allowed
			$value = str_ireplace($neverAllowed, "RM", $value);
			
			// Return the value
			return $value;
		}
		
		protected function _clean_convert_attribute($match)
		{
			return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
		}
		
		protected function _clean_sanitize_naughty_html($matches)
		{
			$str = '&lt;'.$matches[1].$matches[2].$matches[3];
			$str .= str_replace(array('>', '<'), array('&gt;', '&lt;'), $matches[4]);
			return $str;
		}
		
		protected function _clean_html_entity_decode_callback($match)
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
	}
}
?>