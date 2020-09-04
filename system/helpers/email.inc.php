<?php
if(!defined("HELPER_EMAIL_PHP"))
{
	define("HELPER_EMAIL_PHP", 1);
	
	/**
	 * Defined an object to store the email address and the name
	 */
	class Email_Address
	{
		protected $name;
		protected $email;
		
		public function __construct($email = "", $name = "")
		{
			$this->name = trim($name);
			$this->email = trim($email);
		}
	
		public function SetName($name) { $this->name = trim($name); }
		public function SetEmail($email) { $this->email = trim($email); }
		public function GetName() { return $this->name; }
		public function GetEmail() { return $this->email; }
		
		public function Prepare($check_os = false)
		{
			if(!$this->name) return $this->email;
			if($check_os)
			{
				// Sometimes on windows, if you put the name in the recipient, then it breaks
				if(isset($_ENV['OS']) && strpos(strtolower($_ENV['OS']), "windows") !== false)
				{
					return $this->email;
				}
			}
			return "{$this->name} <{$this->email}>";
		}
		
		public function __toString()
		{
			return $this->Prepare();
		}
	}
	
	class Email_Helper
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
		protected $replyTo = null;
		
		public function __construct()
		{
			$this->from = $this->CheckRecipiant(ini_get("sendmail_from"));
			if(!$this->from) $this->from = new Email_Address("noreply@mpconsulting.co.za");
		}
		
		public function ResetRecipiants()
		{
			$this->to = array();
			$this->cc = array();
			$this->bcc = array();
		}
		
		public function ResetAll()
		{
			$this->ResetRecipiants();
			$this->subject = "";
			$this->body_html = "";
			$this->body_text = "";
			$this->attachments = array();
			
			$this->from = $this->CheckRecipiant(ini_get("sendmail_from"));
			if(!$this->from) $this->from = new Email_Address("noreply@mpconsulting.co.za", "Medical Practice Consulting");
		}
		
		protected function CheckRecipiant($obj)
		{
			if(is_a($obj, "Email_Address"))
			{
				return $obj;
			}
			if(!is_string($obj)) return null;
			$obj = trim($obj);
			if($this->ValidEmailAddress($obj))
			{
				return new Email_Address($obj);
			}
			$pos1 = strpos($obj, "<");
			$pos2 = strrpos($obj, ">");
			if($pos1 !== false && $pos2 !== false && $pos1 < $pos2)
			{
				$email = trim(substr($obj, $pos1 + 1, $pos2 - $pos1 - 1));
				if($this->ValidEmailAddress($email)) return new Email_Address($email, substr($obj, 0, $pos1));
			}
			return null;
		}
	
		public function ValidEmailAddress($email)
		{
			return preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email);
		}	
		
		public function AttachFile($path, $name, $inlineImage = false)
		{
			if(!file_exists($path)) return;
			$this->attachments[] = array(
				"path" => $path,
				"name" => $name,
				"type" => mime_content_type($path),
				"inline" => $inlineImage
			);
		}		
		
		public function GetLastMessageID() 
		{ 
			return $this->lastMessageID; 
		}
	
		public function AddTo($to) 
		{ 
			if(is_array($to))
			{
				foreach($to as $recip)
				{
					$this->AddTo($recip);
				}
				return;
			}
			if($recip = $this->CheckRecipiant($to)) 
			{
				$this->to[] = $recip; 
			}
		}
	
		public function AddCc($cc) 
		{ 
			if(is_array($cc))
			{
				foreach($cc as $recip)
				{
					$this->AddCc($recip);
				}
				return;
			}
			if($recip = $this->CheckRecipiant($cc)) 
			{
				$this->cc[] = $recip; 
			}
		}
	
		public function AddBcc($bcc) 
		{ 
			if(is_array($bcc))
			{
				foreach($bcc as $recip)
				{
					$this->AddBcc($recip);
				}
				return;
			}
			if($recip = $this->CheckRecipiant($bcc)) 
			{
				$this->bcc[] = $recip; 
			}
		}
		
		public function SetHtml($html) 
		{ 
			$this->body_html = $html;
			
			$plain_text = strip_tags(str_replace(
				array("<br />", "</div>", "</p>"), 
				array("\n", "</div>\n", "</p>\n"), 
			substr($html, strpos($html, "<body>"))));
			
			$plain_text = trim($plain_text);
			$lines = explode("\n", $plain_text);
			$trimmed_lines = array();
			foreach($lines as $line) 
			{
				$trimmed_lines[] = trim($line);
			}
			$plain_text = implode("\n", $trimmed_lines);
			while(strpos($plain_text, "\n\n\n") !== false)
			{
				$plain_text = str_replace("\n\n\n", "\n\n", $plain_text);
			}
			$this->body_text = $plain_text;
		}
		
		public function SetText($text)
		{
			$this->body_text = $text;
		}
		
		public function SetSubject($subject) 
		{ 
			$this->subject = $subject; 
		} 
		
		public function SetFrom($from) 
		{ 
			if($recip = $this->CheckRecipiant($from)) 
			{
				$this->from = $recip; 
			}
		}
		
		public function SetReplyTo($replyTo) 
		{ 
			if($recip = $this->CheckRecipiant($replyTo)) 
			{
				$this->replyTo = $recip; 
			}
		}
		
		public function Send()
		{
			$mail_to = array();
			
			// To
			$temp = array(); 
			foreach($this->to as $recip) 
			{
				$temp[] = $recip->Prepare(true); 
				$mail_to[] = $recip->Prepare(true);
			}
			$to = implode(", ", $temp);
			
			// CC
			$temp = array(); 
			foreach($this->cc as $recip) 
			{
				$temp[] = $recip->Prepare(true); 
			}
			$cc = trim(implode(", ", $temp));
			
			// BCC
			$temp = array(); 
			foreach($this->bcc as $recip) 
			{
				$temp[] = $recip->Prepare(true); 
			}
			$bcc = trim(implode(", ", $temp));
			
			//$subject = mb_encode_mimeheader($this->subject, "UTF-8", "B", "\n");
			//$random_hash = md5(date("r", time()));
			$from_domain = $this->from ? substr($this->from->getEmail(), strpos($this->from->getEmail(), "@") + 1) : "mpconsulting.co.za";
			$charset = "charset=\"UTF-8\"";
			$this->lastMessageID = "<".uniqid("", true)."@{$from_domain}>";
			$crlf = "\n";
			$mail_to = implode(", ", $mail_to);
			
			$headers = array(
				"From" => $this->from->Prepare(),
				"Reply-To" => $this->replyTo ? $this->replyTo->Prepare() : $this->from->Prepare(),
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
				if($attachment->inline) @$mime->addHTMLImage($attachment->path, $attachment->type);
				else @$mime->addAttachment($attachment->path, $attachment->type, $attachment->name);
			}
			
			$mimeParams = array(
			  'text_encoding' => '7bit',
			  'text_charset'  => 'UTF-8',
			  'html_charset'  => 'UTF-8',
			  'head_charset'  => 'UTF-8'
			);
			
			$body = @$mime->get($mimeParams);
			$headers = @$mime->headers($headers, true);
			$mail = @Mail::factory("mail", "-f {$this->from->GetEmail()}");
			@$mail->send($mail_to, $headers, $body);
			
			return $this->lastMessageID; 
		}
	}
}
?>