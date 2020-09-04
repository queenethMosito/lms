<?php
	include("tcpdf/config/lang/eng.php");
	include("tcpdf/tcpdf.php");
	
	class PDFClass extends TCPDF 
	{
		public function Header()
		{
        	$this->SetTopMargin(10);
		}
		
		public function Footer()
		{
			
		}
	}
?>