<?php
	/**
	 * Class information to go here
	 */
	if(!defined("_CLASS_ROUTER_INC_PHP_"))
	{
		define("_CLASS_ROUTER_INC_PHP_", 1);
		
		class Param
		{
			protected function clean($value)
			{
				if(get_magic_quotes_gpc()) 
		        { 
		        	$value = stripslashes($value); 
		        }
				return $value;
			}
			
			public function post($key, $default = null)
			{
				if(!isset($_POST[$key])) return $default;
				else return $this->clean($_POST[$key]);
			}
			
			public function get($key, $default = null)
			{
				if(!isset($_GET[$key])) return $default;
				else return $this->clean($_GET[$key]);
			}
		}
	}
?>