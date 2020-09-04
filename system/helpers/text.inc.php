<?php
if(!defined("HELPER_TEXT_PHP"))
{
	define("HELPER_TEXT_PHP", 1);
	
	class Text_Helper
	{
		const RANDOM_ALL = 15;
		const RANDOM_LOWER = 1;
		const RANDOM_UPPER = 2;
		const RANDOM_NUMBERS = 4;
		const RANDOM_SYMBOLS = 8;
		const RANDOM_NO_SYMBOLS = 7;
		
		public function PadLeft($string, $length)
		{
			$newString = $string;
			while(strlen($newString) < $length) 
			{
				$newString = "0".$newString;
			}
			return $newString;
		}
		
		/**
		 * Generates a random token with the provided length from the provided subsets
		 * @param int $length The length of the token to generate
		 * @param int $subSets The sub sets to get characters from
		 * @return $string The random token
		 */
		public function Random($length = 8, $subSets = self::RANDOM_NO_SYMBOLS)
		{
			$length = (int) $length;
			if($length < 1) $length = 1;
			if($length > 2048) $length = 2048;
			
			$alphabet = "";
			if($subSets & self::RANDOM_LOWER) $alphabet .= "abcdefghjklmnpqrstuvwxyz";
			if($subSets & self::RANDOM_UPPER) $alphabet .= "ABCDEFGHJKLMNPQRSTUVWXYZ";
			if($subSets & self::RANDOM_NUMBERS) $alphabet .= "0123456789";
			if($subSets & self::RANDOM_SYMBOLS) $alphabet .= "!@#$%()";
			
			$word = "";
			for($i = 0; $i < $length; $i++)
			{
				$word .= $alphabet[rand(0, strlen($alphabet) - 1)];
			}
				
			return $word;
		}
		
		/**
		 * Shortens the selected text to the specified length. Will remove html tags first
		 * Exact length of returned text will be around the specified length as to mantain complete words
		 * @param string $text The text to shorten
		 * @param int $length The required length
		 * @return The shortened text
		 */
		public function CreateSummary($text, $length)
		{
			$content = str_replace(
				array("</p>", "<br />", "<li", "<div", "<img", "<object"), 
				array(" </p>", " <br />", " <li", " <div", " <img", " <object"), 
				$text);
			$content = trim(strip_tags($content));
			
			$left = $right = $length;
			$ws = array(" ", "\t", "\n", "\r");
			$content = str_replace($ws, " ", $content);
			if(strlen($content) > $length)
			{
				if($left < strlen($content))
				{
					while($left > 0 && !in_array($content[$left], $ws)) $left--;
				}
				while($right < strlen($content) && !in_array($content[$right], $ws)) $right++;
				
				$content = ($length - $left) < ($right - $length) ? substr($content, 0, $left) : substr($content, 0, $right);
				$content .= "...";
			}
			
			while(strpos($content, "  ") !== false) $content = str_replace("  ", " ", $content);
			
			return $content;
		}
		
		public function CleanUpWysiwyg($content)
		{
			// Convert all divs to ps
			$content = str_replace(array("<div", "</div"), array("<p", "</p"), $content);
			return $content;
		}
	}
}
?>