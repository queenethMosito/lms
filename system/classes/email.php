<?php
	class EmailAddress
	{
		public $name;
		public $email;
		
		public function __construct($email, $name = "")
		{
			$this->name = $name;
			$this->email = $email;
		}
		
		public function prepare()
		{
			if(!$this->name) return $this->email;
			return "{$this->name} <{$this->email}>";
		}
	}

	class HtmlEmail
	{
		protected $to = array();
		protected $cc = array();
		protected $bcc = array();
		protected $subject = "";
		protected $body = "";
		protected $alt_body = "";
		protected $from = null;
		protected $attachments = array();
		protected $attachments_inline = array();
		
		public function __construct($subject, $body)
		{
			$this->body = $body;
			$this->subject = $subject;
		}
		
		public function __destruct()
		{
			
		}
		
		protected function parseAddress($address, $type)
		{
			if(is_array($address))
			{
				foreach($address as $addr) $this->parseAddress($addr, $type);
			}
			else
			{
				if(is_object($address) && is_a($address, "EmailAddress"))
				{
					$temp = $this->$type;
					$temp[] = $address;
					$this->$type = $temp;
				}
				else
				{
					$temp = $this->$type;
					$temp[] = new EmailAddress($address);
					$this->$type = $temp;
				}
			}
		}
		
		public function addTo($addresses)
		{
			$this->parseAddress($addresses, "to");
		}
		
		public function addCC($addresses)
		{
			$this->parseAddress($addresses, "cc");
		}
		
		public function addBCC($addresses)
		{
			$this->parseAddress($addresses, "bcc");
		}
		
		public function addAttachment($path, $type, $name, $inline_image = false)
		{
			if(!$inline_image)
			{
				$this->attachments[] = array($path, $type, $name);
			}
			else
			{
				$this->attachments_inline[] = array($path, $type, $name);
			}
		}
		
		public function setSubject($subject)
		{
			$this->subject = $subject;	
		}
	
		public function setBody($body)
		{
			$this->body = $body;
		}
		
		public function setAltBody($body)
		{
			$this->alt_body = $body;
		}
		
		public function setFrom($address)
		{
			if(is_object($address) && is_a($address, "EmailAddress"))
			{
				$this->from = $address;
			}
			else
			{
				$this->from = new EmailAddress($address);
			}
		}
		
		public function clearTo()
		{
			$this->to = array();
		}
		
		public function clearCC()
		{
			$this->cc = array();
		}
		
		public function clearBCC()
		{
			$this->bcc = array();
		}
		
		public function send()
		{			
			$temp = array();
			foreach($this->to as $addr) $temp[] = $addr->prepare();
			$to = implode(", ", $temp);
			
			$temp = array();
			foreach($this->cc as $addr) $temp[] = $addr->prepare();
			$cc = implode(", ", $temp);
			
			$temp = array();
			foreach($this->bcc as $addr) $temp[] = $addr->prepare();
			$bcc = implode(", ", $temp);
			
			$random_hash = md5(date('r', time())); 
			$charset = "charset=\"iso-8859-1\"";
		
			// Read attachments if any
			$attachments = array();
			foreach($this->attachments as $attachment)
			{
				if(!file_exists($attachment[0])) 
				{
					print("No such file: {$attachment[0]}<br />");
					continue;
				}
				$data = chunk_split(base64_encode(file_get_contents($attachment[0])));
				$attachments[] = "Content-Type: {$attachment[1]}; name=\"{$attachment[2]}\"  
Content-Transfer-Encoding: base64  
Content-Disposition: attachment  

{$data}";
			}
			
			// Read inline images if any
			$attachments_inline = array();
			foreach($this->attachments_inline as $attachment)
			{
				if(!file_exists($attachment[0])) 
				{
					print("No such file: {$attachment[0]}<br />");
					continue;
				}
				$data = chunk_split(base64_encode(file_get_contents($attachment[0])));
				$attachments_inline[] = "Content-Type: {$attachment[1]}
Content-Transfer-Encoding: base64
Content-ID: <{$attachment[2]}>

{$data}";
			}
			
			// Setup the headers
			$headers = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "X-Sender: mpconsulting.co.za";
			$headers[] = "X-Mailer: MPC";
			$headers[] = "X-Priority: 3 (Normal)";
			$headers[] = "Message-ID: <".uniqid("", true)."@mpconsulting.co.za>";
			if(sizeof($attachments) > 0 || sizeof($attachments_inline) > 0)
			{
				$headers[] = "Content-type: multipart/mixed; boundary=\"PHP-mixed-{$random_hash}\"";
			}
			else
			{
				$headers[] = "Content-type: multipart/alternative; boundary=\"PHP-alt-{$random_hash}\"";
			}
			$headers[] = "From: {$this->from->prepare()}";
			$headers[] = "Reply-To: {$this->from->prepare()}";
			if(trim($cc)) $headers[] = "CC: {$cc}";
			if(trim($bcc)) $headers[] = "Bcc: {$bcc}";
			
			// Get the plain text version
			$plain_text = $this->alt_body;
			
			// Prepare the body
			if(sizeof($attachments) || sizeof($attachments_inline))
			{
				$related = "";
				if(sizeof($attachments_inline))
				{	
$body = "
--PHP-mixed-{$random_hash}
Content-Type: multipart/alternative; boundary=\"PHP-alt-{$random_hash}\"

--PHP-alt-{$random_hash}
Content-Type: text/plain; {$charset}
Content-Transfer-Encoding: 7bit

{$plain_text}


--PHP-alt-{$random_hash}
Content-Type: multipart/related; boundary=\"PHP-related-{$random_hash}\"

--PHP-related-{$random_hash}
Content-Type: text/html; {$charset}
Content-Transfer-Encoding: 7bit

{$this->body}

--PHP-related-{$random_hash}
".implode("\n--PHP-related-{$random_hash}\n", $attachments_inline)."
--PHP-related-{$random_hash}--

--PHP-alt-{$random_hash}--

--PHP-mixed-{$random_hash}  
".implode("\n--PHP-mixed-{$random_hash}\n", $attachments)."
--PHP-mixed-{$random_hash}--
";
				}
				else
				{
$body = "
--PHP-mixed-{$random_hash}
Content-Type: multipart/alternative; boundary=\"PHP-alt-{$random_hash}\"

--PHP-alt-{$random_hash}
Content-Type: text/plain; {$charset}
Content-Transfer-Encoding: 7bit
	
{$plain_text}


--PHP-alt-{$random_hash}
Content-Type: text/html; {$charset}
Content-Transfer-Encoding: 7bit

{$this->body}

--PHP-alt-{$random_hash}--

--PHP-mixed-{$random_hash}
".implode("\n--PHP-mixed-{$random_hash}\n", $attachments)."
--PHP-mixed-{$random_hash}--
";
				}		
			}
			else
			{
$body = "
--PHP-alt-{$random_hash}
Content-Type: text/plain; {$charset}
Content-Transfer-Encoding: 7bit

{$plain_text}

--PHP-alt-{$random_hash}
Content-Type: text/html; {$charset}
Content-Transfer-Encoding: 7bit

{$this->body}

--PHP-alt-{$random_hash}--";
			}
			//print("<div style=\"padding: 3px\">".nl2br(htmlspecialchars(implode("\n", $headers)))."</div>");
			//print("<div style=\"padding: 3px\">".nl2br(htmlspecialchars($body))."</div>");
			
			$body = str_replace("\r\n", "\n", $body);
			return mail($to, $this->subject, $body, implode("\r\n", $headers), "-f {$this->from->email}");
		}
		
		
		public function getToAddresses() { return $this->to; }
		public function getCCAddresses() { return $this->cc; }
		public function getBCCAddresses() { return $this->bcc; }
		public function getFromAddress() { return $this->from; }
		public function getSubject() { return $this->subject; }
		public function getMessageBody() { return $this->body; }
	}
?>