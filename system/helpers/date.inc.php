<?php
if(!defined("HELPER_DATE_PHP"))
{
	define("HELPER_DATE_PHP", 1);
	
	class Date_Helper
	{
		/**
		 * Returns the current system date and optionally time. Takes into account timezones
		 * @param bool $date_only Specify if we only want the date or also the time
		 */
		public function SystemDate($date_only = false)
		{
			if($date_only) 
			{
				return date("Y-m-d");
			}
			else 
			{
				return date("Y-m-d H:i:s");
			}
		}
		
		/**
		 * Returns the current system gmt date and optionally time.
		 * @param bool $date_only Specify if we only want the date or also the time
		 */
		public function GMTDate($date_only = false)
		{
			if($date_only) 
			{
				return gmdate("Y-m-d");
			}
			else 
			{
				return gmdate("Y-m-d H:i:s");
			}
		}
		
		public function gmtToLocal($gmt)
		{
			$time = strtotime($gmt);
			$offset = date("O");
			$hour = substr($offset, 0, 3);
			$mins = substr($offset, 3, 2);
			
			$time += (($hour * 60) + $mins) * 60;
			
			return date("Y-m-d H:i:s", $time);
		}
		
		public function localToGmt($local)
		{
			$time = strtotime($gmt);
			$offset = date("O");
			$hour = substr($offset, 0, 3);
			$mins = substr($offset, 3, 2);
			
			$time -= (($hour * 60) + $mins) * 60;
			
			return date("Y-m-d H:i:s", $time);
		}
		
		public function secondsDifference($date1, $date2)
		{
			if(!is_numeric($date1)) $date1 = strtotime($date1);
			if(!is_numeric($date2)) $date2 = strtotime($date2);
			return abs($date1 - $date2);
		}
		
		public function humanDate($date, $date_only = false, $cap_first_letter = true)
		{
	    	$raw = strtotime($date);
	    	$time = date("H:i", $raw);    	 
	    	
	    	// Check today
	    	if(date("Y-m-d") == date("Y-m-d", $raw)) 
	    	{
	    		$today = $cap_first_letter ? "Today" : "today";
	    		return $date_only ? $today : "{$today} at {$time}";
	    	}
	    	
	    	// Check yesturday
	    	if(date("Y-m-d", strtotime("-1 days")) == date("Y-m-d", $raw)) 
	    	{
	    		$yes = $cap_first_letter ? "Yesterday" : "yesterday";
	    		return $date_only ? $yes : "{$yes} at {$time}";
	    	}
	    	
	    	// Check last 7 days
	    	if(date("Y-m-d", strtotime("-6 days")) < date("Y-m-d", $raw)) return $date_only ? date("l", $raw) : date("l", $raw)." at {$time}";
	    	
	    	// Same year
	    	if(date("Y") == date("Y", $raw)) return $date_only ? date("j F", $raw) : date("j F", $raw)." at {$time}";
	    	
	    	// Else full date
	    	return $date_only ? date("j F Y", $raw) : date("j F Y", $raw)." at {$time}";
		}
			
		public function humanDateSpan($date1, $date2 = null)
		{
			if(!$date2) $date2 = $this->systemDate();
			$temp = $this->secondsDifference($date1, $date2);
			
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
	    	else if($day > 180) // 6 months and longer
	    	{
	    		$mon = floor($day % 365 / 30);
	    		$arr[] = $mon . " month".($mon == 1 ? "" : "s"); 
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
	    	
	    	return implode(" ", $arr);
		}
	}
}
?>