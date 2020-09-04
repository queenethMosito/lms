<?php
if(!defined("_MPC_CLASS_WKHTML_TO_PDF_"))
{
	define("_MPC_CLASS_WKHTML_TO_PDF_", 1);
	
	class WkhtmlToPdf
	{
		public static $MODE_INLINE = "I";
		public static $MODE_DOWNLOAD = "D";
		public static $MODE_STRING = "S";
		public static $MODE_SAVE = "F";
		
		protected $path = "";
		protected $html = "";
		protected $mode = "";
		
		protected $copies = 1;
		protected $grayScale = false;
		protected $lowQuality = false;
		protected $orientation = "Portrait";
		protected $pageSize = "A4";
		protected $title = "";
		protected $margins = array("bottom" => "10mm", "top" => "10mm", "left" => "10mm", "right" => "10mm");
		protected $enableExternalLinks = true;
		protected $pageStartNumber = 0;
		
		protected $header = array("url" => "", "left" => "", "center" => "", "right" => "", "line" => false, "spacing" => 0);
		protected $footer = array("url" => "", "left" => "", "center" => "", "right" => "", "line" => false, "spacing" => 0);
		
		protected $errorMessage = "";
		
		public function SetHtml($html)
		{
			$this->html = $html;
			$this->path = "";
			$this->mode = "HTML";
		}
		
		public function SetPath($path)
		{
			$this->html = "";
			$this->path = $path;
			$this->mode = "PATH";
		}
		
		public function GetErrorMessage()
		{
			return $this->errorMessage;
		}
		
		public function SetCopies($copies)
		{
			$copies = (int) $copies;
			if($copies < 1) $copies = 1;
			$this->copies = $copies;
		}
		
		public function SetGrayScale($grayScale)
		{
			$this->grayScale = $grayScale;
		}
		
		public function SetLowQuality($lowQuality)
		{
			$this->lowQuality = $lowQuality;
		}
		
		public function SetOrientation($orientation)
		{
			if($orientation == "Portrait" || $orientation == "Landscape")
			{
				$this->orientation = $orientation;
			}
		}
		
		public function SetPageSize($pageSize)
		{
			$this->pageSize = $pageSize;
			if(!$this->pageSize)
			{
				$this->pageSize = "A4";
			}
		}
		
		public function SetTitle($title)
		{
			$this->title = $title;
		}
		
		public function SetMargin($value, $where = "all")
		{
			$where = strtolower($where);
			if($where == "all") $where = "top left bottom right";
			if(strstr($where, "top") !== false) $this->margins['top'] = $value;
			if(strstr($where, "bottom") !== false) $this->margins['bottom'] = $value;
			if(strstr($where, "left") !== false) $this->margins['left'] = $value;
			if(strstr($where, "right") !== false) $this->margins['right'] = $value;
		}
		
		public function EnableExternalLinks($state)
		{
			$this->enableExternalLinks = $state;
		}
		
		public function SetHeaderText($content, $position = "center")
		{
			$this->header[$position] = trim($content);
		}
		
		public function SetHeaderUrl($url)
		{
			$this->header['url'] = $url;
		}
		
		public function SetHeaderLine($state)
		{
			$this->header['line'] = $state;
		}
		
		public function SetHeaderSpacing($value)
		{
			$this->header['spacing'] = $value;
		}
		
		public function SetFooterText($content, $position = "center")
		{
			$this->footer[$position] = trim($content);
		}
		
		public function SetFooterUrl($url)
		{
			$this->footer['url'] = $url;
		}
		
		public function SetFooterLine($state)
		{
			$this->footer['line'] = $state;
		}
		
		public function SetFooterSpacing($value)
		{
			$this->footer['spacing'] = $value;
		}
		
		public function SetPageStartNumber($number)
		{
			$number = (int) $number;
			$this->pageStartNumber = $number;
		}
		
		public function Render($mode = "I", $name = "")
		{
			$name = trim($name);
			if(!$name) $name = "document.pdf";
			if(strtolower(substr($name, -4)) != '.pdf') {
				$name .= '.pdf';
			}
			
			// Reset the error message
			$this->errorMessage = "";
			
			$result = true;
			
			// Check that we have some content to work with
			if(!in_array($this->mode, array("HTML", "PATH")))
			{
				$this->errorMessage = "Please call SetHtml or SetPath before calling Render";
				return false;
			}
			
			// Generate a name fro the temp pdf file
			$tempPdfFilename = tempnam(sys_get_temp_dir(), "wkhtmltopdf_");
			
			// Create a temp html if we have content an no path
			if($this->mode == "HTML")
			{
				$tempHtmlFilename = $tempPdfFilename.".html";
				file_put_contents($tempHtmlFilename, $this->html);
				$this->path = $tempHtmlFilename;
			}
			
			// Prep some arguments
			$arguments = array();
			
			if($this->copies > 1) $arguments[] = "--copies {$this->copies}";
			if($this->grayScale) $arguments[] = "-g";
			if($this->lowQuality) $arguments[] = "-l";
			if($this->orientation != "Portrait") $arguments[] = "--orientation {$this->orientation}";
			if($this->pageSize != "A4") $arguments[] = "--page-size {$this->pageSize}";
			if($this->title) $arguments[] = "--title \"{$this->title}\"";
			if(!$this->enableExternalLinks) $arguments[] = "--disable-external-links";
			
			if($this->margins['top'] != "10mm") $arguments[] = "-T {$this->margins['top']}";
			if($this->margins['bottom'] != "10mm") $arguments[] = "-B {$this->margins['bottom']}";
			if($this->margins['left'] != "10mm") $arguments[] = "-L {$this->margins['left']}";
			if($this->margins['right'] != "10mm") $arguments[] = "-R {$this->margins['right']}";
			
			if($this->header['spacing']) $arguments[] = "--header-spacing {$this->header['spacing']}";
			if($this->header['line']) $arguments[] = "--header-line";
			if($this->header['url']) $arguments[] = "--header-html {$this->header['url']}";
			else
			{
				if($this->header['left']) $arguments[] = "--header-left \"{$this->header['left']}\"";
				if($this->header['center']) $arguments[] = "--header-center \"{$this->header['center']}\"";
				if($this->header['right']) $arguments[] = "--header-right \"{$this->header['right']}\"";
			}
			
			if($this->footer['spacing']) $arguments[] = "--footer-spacing {$this->footer['spacing']}";
			if($this->footer['line']) $arguments[] = "--footer-line";
			if($this->footer['url']) $arguments[] = "--footer-html {$this->footer['url']}";
			else
			{
				if($this->footer['left']) $arguments[] = "--footer-left \"{$this->footer['left']}\"";
				if($this->footer['center']) $arguments[] = "--footer-center \"{$this->footer['center']}\"";
				if($this->footer['right']) $arguments[] = "--footer-right \"{$this->footer['right']}\"";
			}
			
			if($this->pageStartNumber) $arguments[] = "--page-offset {$this->pageStartNumber}";
			
			$arguments = sizeof($arguments) ? implode(" ", $arguments) : "";
			
			// Call the wkhtmltopdf application
			$programPath = '/usr/local/bin/wkhtmltopdf';
			if(strncasecmp(PHP_OS, 'WIN', 3) == 0) {
				$programPath = '"c:/program files/wkhtmltopdf/wkhtmltopdf.exe"';
				if(!file_exists($programPath)) {
					$programPath = '"c:/program files (x86)/wkhtmltopdf/wkhtmltopdf.exe"';
				}
			}
			shell_exec("{$programPath} {$arguments} {$this->path} {$tempPdfFilename}");
			
			// Depending on the mode param, do something with the file
			switch($mode)
			{
				case self::$MODE_DOWNLOAD:
					$content = file_get_contents($tempPdfFilename);
					header('Content-Description: File Transfer');
					header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
					header('Pragma: public');
					header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
					header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
					header('Content-Type: application/force-download');
					header('Content-Type: application/octet-stream', false);
					header('Content-Type: application/download', false);
					header('Content-Type: application/pdf', false);
					header('Content-Disposition: attachment; filename="'.basename($name).'";');
					header('Content-Transfer-Encoding: binary');
					header('Content-Length: '.strlen($content));
					print($content);
					break;
				case self::$MODE_SAVE:
					//$result = shell_exec("/bin/mv {$tempPdfFilename} {$name}");
					//if($result !== false) $result = true;
					$content = file_get_contents($tempPdfFilename);
					file_put_contents($name, $content);
					break;
				case self::$MODE_STRING:
					$content = file_get_contents($tempPdfFilename);
					$result = $content;
					break;
				default:
					$content = file_get_contents($tempPdfFilename);
					header('Content-Type: application/pdf');
					header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
					header('Pragma: public');
					header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
					header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
					header('Content-Length: '.strlen($content));
					header('Content-Disposition: inline; filename="'.basename($name).'";');
					print($content);
					break;
			}
			
			// Clean up
			@unlink($tempPdfFilename);
			if($this->mode == "HTML")
			{
				@unlink($tempHtmlFilename);
			}
			
			return $result;
		}
	}
}
?>