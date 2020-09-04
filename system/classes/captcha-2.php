<?php
class Captcha {
	/*
	 * Creates 2 words and returns them in an array
	 * Ensures that the 2 words are at least 5 chars long and different
	 * 30% change second word is a number
	 */
	protected function generateWords() {
		$path = (dirname(__FILE__)).'/';
		$words = explode("\n", file_get_contents($path.'captcha/words.txt'));
		while(true) {
			$index = rand(0, count($words) - 1);
			$firstWord = trim($words[$index]);
			if(strlen($firstWord) >= 5) {
				break;
			}
		}
		
		if(rand(0, 9) < 3) {
			// Number
			$secondWord = rand(10000, 99999);
		}
		else {
			while(true) {
				$index = rand(0, count($words) - 1);
				$secondWord = trim($words[$index]);
				if(strlen($secondWord) >= 5 && $secondWord != $firstWord) {
					break;
				}
			}
		}
		
		$_SESSION['captcha'] = array($firstWord, $secondWord);
		
		return array($firstWord, $secondWord);
	}
	
	public function compareWords($text) {
		if(!isset($_SESSION['captcha'])) {
			return false;
		}
		$text = strtoupper(str_replace(' ', '', trim($text)));
		$sessionText = strtoupper(str_replace(' ', '', trim(implode('', $_SESSION['captcha']))));
		unset($_SESSION['captcha']);
		return $text == $sessionText;
	}
	
	public function generateImage($width, $height) {
		ob_start();
		$path = (dirname(__FILE__)).'/';
		$words = $this->generateWords();
		$image = imagecreate($width, $height);
		
		// Image colours
		$bg_colour = imagecolorallocate($image, 255, 255, 255);
		$fg_colour = imagecolorallocate($image, 20, 30, 70);
		$noiseColours = array();
		for($i = 0; $i < 40; $i++) {
			$noiseColoursBlue[] = imagecolorallocate($image, rand(130, 150), rand(130, 200), rand(130, 250));
			$noiseColoursRed[] = imagecolorallocate($image, rand(100, 250), rand(100, 200), rand(100, 150));
		}
		
		// Random dots for noise
		for($i = 0; $i < ($width * $height) / 2; $i++) {
			imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noiseColoursRed[rand(0, 39)]);
		}
		
		// Random noise
		for($i=0; $i < ($width*$height) / 250; $i++ ) {
			imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noiseColoursBlue[rand(0, 39)]);
		}
		
		// Add text, and calculate font size
		$fonts = array('abduction', 'secret-agent', 'tahoma');
		$font0 = $path.'captcha/'.$fonts[rand(0, count($fonts) - 1)].'.ttf';
		$font1 = $path.'captcha/'.$fonts[rand(0, count($fonts) - 1)].'.ttf';
		
		foreach($words as $index => $word) {
			$fontSize = 20;
			$font = $index == 0 ? $font0 : $font1;
			while(true) {
				$deg = rand(0, 70) - 35;
				$textbox = imagettfbbox($fontSize, $deg, $font, $word);
				if($textbox[4] <= ($width / 2 * 0.8) && $textbox[5] < ($height * 0.8)) break;
				$fontSize--;
			}
			$x = ($width / 2 - $textbox[4]) / 2 + ($width / 2 * $index);
			$y = ($height - $textbox[5]) / 2;
			imagettftext($image, $fontSize, $deg, $x, $y, $fg_colour, $font, $word);
		}
		
		// Check for errors
		$output = ob_get_clean();
		if($output) {
			die($output);
		}
		
		// Output
		header('Content-Type: image/jpeg');
		imagejpeg($image);
		imagedestroy($image);
		die();
	}
}
