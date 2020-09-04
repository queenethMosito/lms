<?php
if(!defined("_CLASS_CAPTCHA_"))
{
	define("_CLASS_CAPTCHA_", 1);
	
	class CaptchaClass
	{		
		public function generateImage($width, $height, $text)
		{
			$font_index = rand(0, 3);
			if($font_index == 0)
			{
				//$font = "Anorexia.ttf";
				$font = FRAMEWORK_PATH."public/fonts/Anorexia.ttf";
				$fontSize = $height * 0.45;
			}
			else
			{
				//$font = "SecretAgent.ttf";
				$font = FRAMEWORK_PATH."public/fonts/SecretAgent.ttf";
				$fontSize = $height * 0.45;
			}
			$font = FRAMEWORK_PATH."public/fonts/tahoma.ttf";
			$fontSize = $height * 0.55;
			$image = imagecreate($width, $height);
			
			// Image colours
			$bg_colour = imagecolorallocate($image, 255, 255, 255);
			$fg_colour = imagecolorallocate($image, 20, 30, 70);
			$noice_colour = imagecolorallocate($image, 150, 180, 200);
			
			// Random dots for noise
			for($i = 0; $i < ($width * $height) / 3; $i++)
			{
				imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noice_colour);
			}
			
			// Random noise
			for($i=0; $i < ($width*$height) / 150; $i++ ) 
			{
				imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noice_colour);
			}
			
			// Add text
			$textbox = imagettfbbox($fontSize, 0, $font, $text);
			$x = ($width - $textbox[4]) / 2;
			$y = ($height - $textbox[5]) / 2;
			
			imagettftext($image, $fontSize, 0, $x, $y, $fg_colour, $font, $text);
			
			// Output
			header("Content-Type: image/jpeg");
			imagejpeg($image);
			imagedestroy($image);
		}
	}
	
	class CCaptcha_Class extends CaptchaClass {}
}
?>