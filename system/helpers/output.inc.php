<?php
if(!defined("FRAMEWORK_HELPER_OUTPUT"))
{
	define("FRAMEWORK_HELPER_OUTPUT", 1);
	
	class Output_Helper
	{
		private static $headerContent = "";
		private static $footerContent = "";
		
		public static function GetHeaderContent()
		{
			return self::$headerContent;
		}
		
		public function AppendHeaderContent($content)
		{
			if(self::$headerContent)
			{
				self::$headerContent .= "\n";
			}
			self::$headerContent .= $content;
		}
	}
}