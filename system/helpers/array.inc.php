<?php
if(!defined("HELPER_ARRAY"))
{
	define("HELPER_ARRAY", 1);
	
	class Array_Helper
	{
		public function Select($array, $key, $default = null)
		{
			if(!is_array($array))
			{
				return $default;
			}
			return isset($array[$key]) ? $array[$key] : $default;
		}
		
		public function Random($array)
		{
			if(!is_array($array) || sizeof($array) < 0)
			{
				return null;
			}
			$index = rand(0, sizeof($array) - 1);
			return $array[$index];
		}
		
		public function SelectSubset($array, $keys, $default = null)
		{
			$return = array();
			if(!is_array($array) || !is_array($keys)) return $return;
			foreach($keys as $key)
			{
				$return[$key] = isset($array[$key]) ? $array[$key] : $default;
			}
			return $return;
		}
	}
}
?>
