<?php
	include("fpdf/html2fpdf.php");
	
	class PDF extends HTML2FPDF 
	{
		function __construct($orientation='P',$unit='mm',$format='A4')
		{
			parent::HTML2FPDF($orientation, $unit, $format);
			
			$this->SetMargins(10, 10, 10);
		}
		
		function Header()
		{			
			global $config;
			$width = $this->pgwidth;
			$this->Image("{$config['root_physical']}images/logo.jpg", 0, 0, $width + 20);
			
			$this->Ln(10);
		}
		
		function Footer()
		{
			$this->SetY(-15);
			$this->SetFont("Arial", "B", 12);
			$this->Cell(0,10,"Page ".$this->PageNo()."/{nb}", 0, 0,"C");
		}
	}
?>