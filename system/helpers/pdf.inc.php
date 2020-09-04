<?php
if(!defined("HELPER_PDF"))
{
	define("HELPER_PDF", 1);
	
	/**
	 * PDF Wrapper class
	 * 
	 * Houses functions related to PDF's
	 * Makes use of the external program pdftk to handle some operations
	 * 
	 * @author Christopher Trew
	 * @package Utils
	 */
	class PDF_Helper
	{
		/**
		 * Converts the image to pdf
		 * @param string $path The path to the image
		 * @param string $newPath The path to save the PDF, if blank then overwrite the image
		 */
		public function ImageToPDF($path, $newPath = "")
		{
			$info = getimagesize($path);
			$width = $info[0];
			$height = $info[1];
			$ratio = $info[0] / $info[1];
			
			$mode = "Potrait";
			$maxWidth = 1100;
			$maxHeight = 1700;
			$pageHeight = "280mm";
			
			if($ratio > 1.0) 
			{
				$mode = "Landscape";
				list($maxHeight, $maxWidth) = array($maxWidth, $maxHeight);	
				$pageHeight = "198mm";
			}
			
			if($width > $maxWidth)
			{
				$height = $height / $width * $maxWidth;
				$width = $maxWidth;
			}
			
			if($height > $maxHeight)
			{
				$width = $width / $height * $maxHeight;
				$height = $maxHeight;
			}
			
			$html = "
				<html>
				<body style=\"height: {$pageHeight}; padding: 0px; margin: 0px\">
				<table style=\"width: 100%; height: 100%;\" cellpadding=\"0\" cellspacing=\"0\">
				<tr><td>
				<center>
					<img src=\"{$path}\" style=\"width: {$width}px; height: {$height}px\" />
				</center>
				</td></tr>
				</table>
				</body>
				</html>
			";
			
			include(CLASS_PATH."wkhtmltopdf.php");
			$wkhtmltopdf = new WkhtmlToPdf();
			$wkhtmltopdf->SetHtml($html);
			$wkhtmltopdf->SetMargin("0mm", "all");
			$wkhtmltopdf->SetOrientation($mode);
			$wkhtmltopdf->Render(WkhtmlToPdf::$MODE_SAVE, $newPath ? $newPath : $path);
		}
	}
}