<?php
if(!defined("_CLASSES_TREW_MAIL_"))
{
	define("_CLASSES_TREW_MAIL_", 1);

	define("TREWMAIL_ROOT", dirname(__FILE__)."/");
	if(isset($_ENV['OS']) && strpos(strtolower($_ENV['OS']), "windows") !== false)
	{
		define("TREWMAIL_SERVER_WINDOWS", true);
	}
	else
	{
		define("TREWMAIL_SERVER_WINDOWS", false);
	}
	
	class TrewMailAddress
	{
		protected $name;
		protected $email;
		
		public function __construct($email = "", $name = "")
		{
			$this->name = trim($name);
			$this->email = trim($email);
		}
	
		public function setName($name) { $this->name = trim($name); }
		public function setEmail($email) { $this->email = trim($email); }
		public function getName() { return $this->name; }
		public function getEmail() { return $this->email; }
		
		public function prepare($check_os = false)
		{
			if(!$this->name) return $this->email;
			if($check_os && TREWMAIL_SERVER_WINDOWS) return $this->email;
			return "{$this->name} <{$this->email}>";
		}
	}
	
	class TrewMail
	{
		protected $to = array();
		protected $cc = array();
		protected $bcc = array();
		protected $from = null;
		protected $subject = "";
		protected $body_html = "";
		protected $body_text = "";
		protected $attachments = array();
		protected $lastMessageID = "";
		
		public function __construct()
		{
			$this->from = $this->checkRecipiant(ini_get("sendmail_from"));
			if(!$this->from) $this->from = new TrewMailAddress("noreply@mpconsulting.co.za");
		}
		
		public function validEmailAddress($email)
		{
			return preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email);
		}
		
		protected function checkRecipiant($obj)
		{
			if(is_a($obj, "TrewMailAddress"))
			{
				return $obj;
			}
			if(!is_string($obj)) return null;
			$obj = trim($obj);
			if($this->validEmailAddress($obj))
			{
				return new TrewMailAddress($obj);
			}
			$pos1 = strpos($obj, "<");
			$pos2 = strrpos($obj, ">");
			if($pos1 !== false && $pos2 !== false && $pos1 < $pos2)
			{
				$email = trim(substr($obj, $pos1 + 1, $pos2 - $pos1 - 1));
				if($this->validEmailAddress($email)) return new TrewMailAddress($email, substr($obj, 0, $pos1));
			}
			return null;
		}
	
		public function addTo($to) { if($recip = $this->checkRecipiant($to)) $this->to[] = $recip; }
		public function addCc($cc) { if($recip = $this->checkRecipiant($cc)) $this->cc[] = $recip; }
		public function addBcc($bcc) { if($recip = $this->checkRecipiant($bcc)) $this->bcc[] = $recip; }
		public function setHtml($html) { $this->body_html = $html; }
		public function setText($text) { $this->body_text = $text; } 
		public function setSubject($subject) { $this->subject = $subject; } 
		public function setFrom($form) { if($recip = $this->checkRecipiant($form)) $this->from = $recip; }
		
		public function attachFile($path, $name, $inlineImage = false)
		{
			if(!file_exists($path)) return;
			$this->attachments[] = array(
				"path" => $path,
				"name" => $name,
				"type" => mime_content_type($path),
				"inline" => $inlineImage
			);
		}
		
		public function getLastMessageID() { return $this->lastMessageID; }
		
		public function send()
		{
			$mail_to = array();
			
			// To
			$temp = array(); 
			foreach($this->to as $recip) 
			{
				$temp[] = $recip->prepare(true); 
				$mail_to[] = $recip->prepare(true);
			}
			$to = implode(", ", $temp);
			
			// CC
			$temp = array(); 
			foreach($this->cc as $recip) 
			{
				$temp[] = $recip->prepare(true); 
			}
			$cc = trim(implode(", ", $temp));
			
			// BCC
			$temp = array(); 
			foreach($this->bcc as $recip) 
			{
				$temp[] = $recip->prepare(true); 
			}
			$bcc = trim(implode(", ", $temp));
			
			//$subject = mb_encode_mimeheader($this->subject, "UTF-8", "B", "\n");
			//$random_hash = md5(date("r", time()));
			$from_domain = $this->from ? substr($this->from->getEmail(), strpos($this->from->getEmail(), "@") + 1) : "no-domain.com";
			$charset = "charset=\"iso-8859-1\"";
			$this->lastMessageID = "<".uniqid("", true)."@{''}>";
			$crlf = "\n";
			$mail_to = implode(", ", $mail_to);
			
			$headers = array(
				"From" => $this->from->prepare(),
				"Reply-To" => $this->from->Prepare(),
				"MIME-Version" => "1.0",
				"X-Mailer" => "PHP/".phpversion(),
				"Message-ID" => $this->lastMessageID,
				"Subject" => $this->subject,
				"To" => $to
			);
			if(strlen($cc)) $headers["CC"] = $cc;
			if(strlen($bcc)) $headers["BCC"] = $bcc;
			
			$text = wordwrap($this->body_text, 70);
			
			include_once("Mail.php");
			include_once("Mail/mime.php");
			$mime = new Mail_mime($crlf);
			$mime->setTXTBody($text);
			$mime->setHTMLBody($this->body_html);
			
			// Attachments here
			foreach($this->attachments as $attachment)
			{
				$attachment = (object) $attachment;
				if($attachment->inline) $mime->addHTMLImage($attachment->path, $attachment->type);
				else $mime->addAttachment($attachment->path, $attachment->type, $attachment->name);
			}
			
			$body = $mime->get();
			$headers = $mime->headers($headers, true);
			$mail = &Mail::factory("mail", "-f {$this->from->GetEmail()}");
			$mail->send($mail_to, $headers, $body);
		}
	}
}
?>