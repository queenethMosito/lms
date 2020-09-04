<?php
if(!defined("_CLASSES_TREW_MAIL_TEMPLATE_"))
{
	define("_CLASSES_TREW_MAIL_TEMPLATE_", 1);
	include("trewmail.php");
	
	class TrewMailTemplate extends TrewMail 
	{
		protected $masterName;
		protected $templateName;
		protected $params;
		
		public function __construct($masterName, $templateName, $params)
		{
			$this->masterName = $masterName;
			$this->templateName = $templateName;
			$this->params = $params;
		}
		
		public function send()
		{
			$params = array();
			$params['root_web'] = WEB_PATH;
			$params['root_physical'] = FRAMEWORK_PATH;
			$params = array_merge($this->params, $params);

			// HTML part
			$html = file_get_contents(SYSTEM_PATH."email_templates/{$this->templateName}.html");
			$html = $this->performReplace($html, $params);
			$params['content'] = $html;
			$html = file_get_contents(SYSTEM_PATH."email_templates/master_emails/{$this->masterName}.html");
			$html = $this->performReplace($html, $params);
			$this->setHtml($html);		
			
			// Text part
			$text = file_get_contents(SYSTEM_PATH."email_templates/{$this->templateName}.txt");
			$text = $this->performReplace($text, $params);
			$params['content'] = $text;
			$text = file_get_contents(SYSTEM_PATH."email_templates/master_emails/{$this->masterName}.txt");
			$text = $this->performReplace($text, $params);
			$this->setText($text);	
			
			// Attach all files
			include(SYSTEM_PATH."email_templates/{$this->templateName}.php");
			foreach($email_files as $file) $this->attachFile($file[0], $file[1], $file[2]);
			
			include(SYSTEM_PATH."email_templates/master_emails/{$this->masterName}.php");
			foreach($master_email_files as $file) $this->attachFile($file[0], $file[1], $file[2]);
			
			// Send the email
			return parent::send();
		}
		
		protected function performReplace($content, $params)
		{
			foreach($params as $key => $value)
			{
				$look = "$%{$key}%$";
				$content = str_replace($look, $value, $content);
			}
			return $content;
		}
	}
}