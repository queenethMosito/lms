<?php
if(!defined("_CLASS_UTIL_IMAGES_PHP_"))
{
	define("_CLASS_UTIL_IMAGES_PHP_", 1);
	
	class Util_Images
	{
		const TYPE_GIF = 1;
		const TYPE_PNG = 2;
		const TYPE_JPG = 3;
		
		const MODE_FILE = 1;
		const MODE_UPLOAD = 2;
		
		const CORNER_TOP_LEFT = 1;
		const CORNER_TOP_RIGHT = 2;
		const CORNER_BOTTOM_LEFT = 4;
		const CORNER_BOTTOM_RIGHT = 8;
		
		const CORNER_TOP = 3;
		const CORNER_LEFT = 5;
		const CORNER_RIGHT = 10;
		const CORNER_BOTTOM = 12;
		
		const CORNER_ALL = 15;
		
		protected $original_type;
		protected $original_content = null;
		protected $original_width;
		protected $original_height;
		protected $original_mime;
		
		protected $new_content = null;
		protected $new_width;
		protected $new_height;
		
		public function __construct()
		{
			ini_set("memory_limit", "128M");
		}
		
		public function __destruct()
		{
			if($this->original_content) imagedestroy($this->original_content);
			if($this->new_content) imagedestroy($this->new_content);
		}
		
		public function loadImage($data, $mode = Util_Images::MODE_FILE)
		{
			$path = $mode == Util_Images::MODE_FILE ? $data : $data['tmp_name'];
			
			// Make sure the file exists
			if(!file_exists($path)) throw new Exception("No such file");
			
			// Determine the type
			$size = getimagesize($path);
			$type = $mode == Util_Images::MODE_FILE ? $size['mime'] : $data['type'];
			$type = strtolower($type);
			$this->original_mime = $type;
			if($type == "image/jpg" || $type == "image/jpeg" || $type == "image/pjpeg") $this->original_type = Util_Images::TYPE_JPG;
			elseif($type == "image/png") $this->original_type = Util_Images::TYPE_PNG;
			elseif($type == "image/gif") $this->original_type = Util_Images::TYPE_GIF;
			else throw new Exception("Invalid file type");
			
			// Cleanup id needed
			if($this->original_content) imagedestroy($this->original_content);
				
			// Grab the content
			if($this->original_type == Util_Images::TYPE_GIF)
				$this->original_content = imagecreatefromgif($path);
			elseif($this->original_type == Util_Images::TYPE_JPG)
				$this->original_content = imagecreatefromjpeg($path);
			else
				$this->original_content = imagecreatefrompng($path);
			
			// Grab the size
			$this->original_width = $size[0];
			$this->original_height = $size[1];
			
			// Reset the image
			$this->reset();
		}
		
		public function reset()
		{
			$this->new_width = $this->original_width;
			$this->new_height = $this->original_height;
			if($this->new_content) imagedestroy($this->new_content);
			$this->new_content = imagecreatetruecolor($this->new_width, $this->new_height);
			imagecopy($this->new_content, $this->original_content, 0, 0, 0, 0, $this->original_width, $this->original_height);
		}
		
		public function getWidth()
		{
			return $this->original_width;
		}
		
		public function getHeight()
		{
			return $this->original_height;
		}
		
		public function resize($newWidth, $newHeight = -1, $keepScale = true, $background = array(255, 255, 255))
		{
			$this->resizeImage($newWidth, $newHeight, $keepScale, $background);
		}
		
		public function resizeImage($newWidth, $newHeight = -1, $keepScale = true, $background = array(255, 255, 255))
		{
			if($newWidth < 1 && $newHeight < 1) return;
			$current_width = $this->new_width;
			$current_height = $this->new_height;
			$current_content = imagecreatetruecolor($current_width, $current_height);
			imagecopy($current_content, $this->new_content, 0, 0, 0, 0, $current_width, $current_height);
			
			if($keepScale && $newWidth > 0 && $newHeight > 0)
			{
				$this->new_height = $newHeight;
				$this->new_width = $newWidth;
				imagedestroy($this->new_content);
				$this->new_content = imagecreatetruecolor($newWidth, $newHeight);
				
				// Fill with the background colour
				$back = imagecolorallocate($this->new_content, $background[0], $background[1], $background[2]);
				imagefill($this->new_content, 0, 0, $back);
				
				// Work out ratios
				$ratio_width = $newWidth / $current_width;
				$ratio_height = $newHeight / $current_height;
				
				if($ratio_width < $ratio_height)
				{
					$dest_x = 0;
					$temp_height = $current_height * $ratio_width;
					$dest_y = ceil(($this->new_height - $temp_height) / 2);
					imagecopyresampled($this->new_content, $current_content, $dest_x, $dest_y, 0, 0, $this->new_width, $temp_height, $current_width, $current_height);
				}
				else
				{
					$dest_y = 0;
					$temp_width = $current_width * $ratio_height;
					$dest_x = ceil(($this->new_width - $temp_width) / 2);
					imagecopyresampled($this->new_content, $current_content, $dest_x, $dest_y, 0, 0, $temp_width, $this->new_height,$current_width, $current_height);
				}
				
				imagedestroy($current_content);
			}
			else
			{
				if($newHeight > 0 && $newWidth > 0)
				{	
					$this->new_width = $newWidth;
					$this->new_height = $newHeight;
				}
				elseif($newHeight == -1)
				{
					$this->new_width = $newWidth;
					$ratio = $newWidth / $current_width;
					$this->new_height = ceil($current_height * $ratio);
				}
				else
				{
					$this->new_height = $newHeight;
					$ratio = $newHeight / $current_height;
					$this->new_width = ceil($current_width * $ratio);
				}
				
				imagedestroy($this->new_content);
				$this->new_content = imagecreatetruecolor($this->new_width, $this->new_height);
				imagecopyresampled($this->new_content, $current_content, 0, 0, 0, 0, $this->new_width, $this->new_height, $current_width, $current_height);
				imagedestroy($current_content);
			}
		}
		
		public function crop($x, $y, $width, $height)
		{
			$current_content = imagecreatetruecolor($this->new_width, $this->new_height);
			imagecopy($current_content, $this->new_content, 0, 0, 0, 0, $this->new_width, $this->new_height);
			imagedestroy($this->new_content);
			$this->new_content = imagecreatetruecolor($width, $height);
			imagecopy($this->new_content, $current_content, 0, 0, $x, $y, $width, $height);
			$this->new_width = $width;
			$this->new_height = $height;
			imagedestroy($current_content);
		}
		
		public function greyScale()
		{
			imagefilter($this->new_content, IMG_FILTER_GRAYSCALE);
		}
		
		public function rotateLeft($background = array(255, 255, 255))
		{
			$back = imagecolorallocate($this->new_content, $background[0], $background[1], $background[2]);
			$current_content = imagecreatetruecolor($this->new_width, $this->new_height);
			imagecopy($current_content, $this->new_content, 0, 0, 0, 0, $this->new_width, $this->new_height);
			imagedestroy($this->new_content);
			$this->new_content = imagerotate($current_content, 90.0, $back);
			imagedestroy($current_content);
			list($this->new_height, $this->new_width) = array($this->new_width, $this->new_height);
		}
		
		public function rotateRight($background = array(255, 255, 255))
		{
			$back = imagecolorallocate($this->new_content, $background[0], $background[1], $background[2]);
			$current_content = imagecreatetruecolor($this->new_width, $this->new_height);
			imagecopy($current_content, $this->new_content, 0, 0, 0, 0, $this->new_width, $this->new_height);
			imagedestroy($this->new_content);
			$this->new_content = imagerotate($current_content, -90.0, $back);
			imagedestroy($current_content);
			list($this->new_height, $this->new_width) = array($this->new_width, $this->new_height);
		}
		
		public function rotate180($background = array(255, 255, 255))
		{
			$back = imagecolorallocate($this->new_content, $background[0], $background[1], $background[2]);
			$current_content = imagecreatetruecolor($this->new_width, $this->new_height);
			imagecopy($current_content, $this->new_content, 0, 0, 0, 0, $this->new_width, $this->new_height);
			imagedestroy($this->new_content);
			$this->new_content = imagerotate($current_content, 180.0, $back);
			imagedestroy($current_content);
			list($this->new_height, $this->new_width) = array($this->new_width, $this->new_height);
		}
		
		public function flipHorizontal()
		{
			$w = $this->new_width;
			$h = $this->new_height;
			$current_content = imagecreatetruecolor($w, $h);
			imagecopyresampled($current_content, $this->new_content, 0, 0, 0, 0, $w, $h, $w, $h);
			imagecopyresampled($this->new_content, $current_content, 0, 0, $w - 1, 0, $w, $h, 0 - $w, $h);
			imagedestroy($current_content);
		}
		
		public function flipVertical()
		{
			$w = $this->new_width;
			$h = $this->new_height;
			$current_content = imagecreatetruecolor($w, $h);
			imagecopyresampled($current_content, $this->new_content, 0, 0, 0, 0, $w, $h, $w, $h);
			imagecopyresampled($this->new_content, $current_content, 0, 0, 0, $h - 1, $w, $h, $w, 0 - $h);
			imagedestroy($current_content);
		}
		
		public function applyRoundCorner($radius = 10, $target = self::CORNER_ALL, 
			$background = array(255, 255, 255), $borderWidth = 0, $borderColor = array(0, 0, 0))
		{
			// Create the corner mask
			$corner = imagecreatetruecolor($radius, $radius);
			$clearColor = imagecolorallocate($corner, 1, 0, 0);
			$solidColor = imagecolorallocate($corner, $background[0], $background[1], $background[2]);
			
			imagecolortransparent($corner, $clearColor);
			imagefill($corner, 0, 0, $solidColor);
			
			// Add border if specified
			if($borderWidth)
			{
				
				$color = imagecolorallocate($corner, $borderColor[0], $borderColor[1], $borderColor[2]);
				imagefilledellipse($corner, $radius, $radius, $radius * 2, $radius * 2, $color);
			}
			imagefilledellipse($corner, $radius, $radius, 
				($radius - $borderWidth) * 2, ($radius - $borderWidth) * 2, $clearColor);
			
			// Apply the corners to the image
			$w = $this->new_width;
			$h = $this->new_height;
			
			// Top left
			if($target & self::CORNER_TOP_LEFT)
			{
				imagecopymerge($this->new_content, $corner, 0, 0, 0, 0, $radius, $radius, 100);
			}		

			// Bottom left
			$corner = imagerotate($corner, 90, 0);
			if($target & self::CORNER_BOTTOM_LEFT)
			{
				imagecopymerge($this->new_content, $corner, 0, $h - $radius, 0, 0, $radius, $radius, 100);
			}	

			// Bottom right
			$corner = imagerotate($corner, 90, 0);
			if($target & self::CORNER_BOTTOM_RIGHT)
			{
				imagecopymerge($this->new_content, $corner, $w - $radius, $h - $radius, 0, 0, $radius, $radius, 100);
			}	

			// Top right
			$corner = imagerotate($corner, 90, 0);
			if($target & self::CORNER_TOP_RIGHT)
			{
				imagecopymerge($this->new_content, $corner, $w - $radius, 0, 0, 0, $radius, $radius, 100);
			}	
		}
		
		public function addBorder($width = 1, $color = array(0, 0, 0))
		{
			$w = $this->new_width;
			$h = $this->new_height;
			$color = imagecolorallocate($this->new_content, $color[0], $color[1], $color[2]);
			for($i = 0; $i < $width; $i++)
			{
				imagerectangle($this->new_content, $i, $i, $w - 1 - $i, $h - 1 - $i, $color);
			}
		}
		
		public function saveImage($path, $type = null, $quality = 80)
		{
			if($type == null)
			{
				$type = $this->original_type;
			}
			if($path == null)
			{
				ob_start();
			}
			switch($type)
			{
				case Util_Images::TYPE_GIF:
					imagegif($this->new_content, $path);
					break;
				case Util_Images::TYPE_JPG:
					imagejpeg($this->new_content, $path, $quality);
					break;
				case Util_Images::TYPE_PNG:
					imagepng($this->new_content, $path, $quality);
					break;
			}
			if($path == null)
			{
				return ob_get_clean();
			}
		}
	}
}
?>