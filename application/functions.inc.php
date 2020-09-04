<?php
	/**
	 * Gets the current logged in user
	 * @return Object
	 */
	function getCurrentUser() {
		$userID = (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) ? (int) $_SESSION['user']['id'] : 0;
		$connection = Application::GetApplication()->getConnection();
		$user = $connection->selectSingle('users', $userID);

		return $user;
	}


	function hashPassword($salt, $password) {
		$salt = trim($salt);
		$password = trim($password);

		return '*'.md5($salt.$password);
	}

	function extractInitials($name) {
		$names = explode(' ', $name);
		$initials = '';
		foreach($names as $part) {
			$part = trim($part);
			if(strlen($part) > 0) {
				$initials .= substr($part, 0, 1);
			}
		}
		return strtoupper($initials);
	}

	function bytesToHumanSize($size) {
		if($size < 1000) {
			return $size.'B';
		}
		$size = number_format($size / 1024, 1, '.', '');
		if($size < 1000) {
			return $size.'K';
		}
		$size = number_format($size / 1024, 1, '.', ' ');
		return $size.'M';
	}

	function numberFormat($amount, $showSymbol = true) {
		if($amount < 0.0) {
			if(!$showSymbol) {
				return '('.number_format(abs($amount), 2, '.', ' ').')';
			}
			return '(R '.number_format(abs($amount), 2, '.', ' ').')';
		}
		else {
			if(!$showSymbol) {
				return number_format(abs($amount), 2, '.', ' ');
			}
			return 'R '.number_format(abs($amount), 2, '.', ' ');
		}
	}

	function reviewStateOptions() {
		return array(
			0 => 'Pending',
			1 => 'Approved',
			-1 => 'Bounced',
			-2 => 'Declined'
		);
	}

	function ecsaLevelOptions() {
		return array(
			'' => '',
			'None' => 'None',
			'Candidate Engineer' => 'Candidate Engineer',
			'Candidate Engineering Technician' => 'Candidate Engineering Technician',
			'Candidate Engineering Technologist' => 'Candidate Engineering Technologist',
			'Professional Engineer' => 'Professional Engineer',
			'Professional Engineering Technician' => 'Professional Engineering Technician',
			'Professional Engineering Technologist' => 'Professional Engineering Technologist'
		);
	}

	function highestQualificationOptions() {
		return array(
			'' => '',
			'None' => 'None',
			'BSc.Eng' => 'BSc.Eng',
			'B.Eng' => 'B.Eng',
			'B.Eng(Hons)' => 'B.Eng(Hons)',
			'M.Eng or MSc.Eng' => 'M.Eng or MSc.Eng',
			'PhD' => 'PhD',
			'B.Tech' => 'B.Tech',
			'M.Tech' => 'M.Tech',
			'D.Tech' => 'D.Tech',
			'D.Sc' => 'D.Sc',
			'DBA' => 'DBA',
			'MBA' => 'MBA',
			'Dipl' => 'Dipl'
		);
	}

	function institutionList() {
		return array(
			'' => '',
			'University of Pretoria' => 'University of Pretoria',
			'Stellenbosch University' => 'Stellenbosch University',
			'University of Witwatersrand' => 'University of Witwatersrand',
			'University of Johannesburg' => 'University of Johannesburg',
			'University of Cape Town' => 'University of Cape Town',
			'University of Orange Free State' => 'University of Orange Free State',
			'Northwest University' => 'Northwest University',
			'UNISA' => 'UNISA',
			'Tshwane University of Technology' => 'Tshwane University of Technology',
			'Nelson Mandela Metropolitan University' => 'Nelson Mandela Metropolitan Uni...',
			'Durban University of Technology' => 'Durban University of Technology',
			'Vaal University of Technology' => 'Vaal University of Technology',
			'Other' => 'Other'
		);
	}

	function employmentTypeOptions() {
		return array(
			'' => '',
			'Employed' => 'Employed',
			'Employer' => 'Employer',
			'Self-Employed' => 'Self-Employed',
			'Retired' => 'Retired',
			'Student' => 'Student',
			'None' => 'None',
			'Unknown' => 'Unknown'
		);
	}

	function yearOptions() {
		return array(
			'1 to 2 yrs' => '1 to 2 yrs',
			'2 to 5 yrs' => '2 to 5 yrs',
			'5 to 10 yrs' => '5 to 10 yrs',
			'more than 10 yrs' => 'more than 10 yrs',
			'None' => 'None'
		);
	}

	function yearAnyOptions() {
		return array(
			'any' => 'Any',
			'1 to 2 yrs' => '1 to 2 yrs',
			'2 to 5 yrs' => '2 to 5 yrs',
			'5 to 10 yrs' => '5 to 10 yrs',
			'more than 10 yrs' => 'more than 10 yrs',
			'None' => 'None'
		);
	}

	function categoryOptions() {
		return array(
			'Project Management' => 'Project Management',
			'Manufacturing, Production and Distribution' => 'Manufacturing, Production and Distribution',
			'Supply Chain Management' => 'Supply Chain Management',
			'Productivity, Methods and Process Engineering' => 'Productivity, Methods and Process Engineering',
			'Quality Measurement and Improvement' => 'Quality Measurement and Improvement',
			'Program Management' => 'Program Management',
			'Ergonomics/Human Factors' => 'Ergonomics/Human Factors',
			'Technology Development and Transfer' => 'Technology Development and Transfer',
			'Strategic Planning' => 'Strategic Planning',
			'Management of Change' => 'Management of Change',
			'Financial Engineering' => 'Financial Engineering'
		);
	}

	function employmentOptions() {
		return array(
			'Perm &amp; Temp' => 'Perm &amp; Temp',
			'Permanent' => 'Permanent',
			'Temporary' => 'Temporary'
		);
	}

	function employmentEquityOptions() {
		return array(
			'Asian' => 'Asian',
			'Black' => 'Black',
			'Coloured' => 'Coloured',
			'Indian' => 'Indian',
			'White' => 'White'
		);
	}




	function yesNoOptions() {
		return array('yes' => 'Yes', 'no' => 'No');
	}

	function titleOptions() {
		return array('' => '', 'Mr' => 'Mr', 'Mrs' => 'Mrs', 'Ms' => 'Ms', 'Miss' => 'Miss', 'Prof' => 'Prof', 'Dr' => 'Dr');
	}
	
	function raceOptions() {
		return array('' => '', 'Caucasian' => 'Caucasian', 'Indian' => 'Indian', 'Coloured' => 'Coloured', 'Black' => 'Black', 'Asian' => 'Asian');
	}

	function genderOptions() {
		return array('' => '', 'M' => 'Male', 'F' => 'Female');
	}

	function idTypeOptions() {
		return array(1 => 'ID number', 2 => 'Passport number');
	}

	function invoiceDeliveryOptions() {
		return array('E-mail' => 'E-mail', 'Postal' => 'Postal');
	}

	function sendUserEmailPaymentDue($user, $subject, $content, $attachments = array()) {
		$attachments[] = array('inline' => true, 'path' => APPLICATION_PATH.'templates/payment-due/payment-due.jpg', 'name' => 'Payment due');
		sendUserEmail($user, $subject, $content, $attachments, 'payment-due');
	}

	function sendUserEmail($user, $subject, $content, $attachments = array(), $templateName = 'user-email') {
		require_once(SYSTEM_PATH.'helpers/email.inc.php');
		$hEmail = new Email_Helper();

		// Load and prepare the template
		$templatePath = APPLICATION_PATH.'templates/';
		$template = file_get_contents($templatePath.$templateName.'.html');
		$template = str_replace(array(
			'[[path]]',
			'[[name]]',
			'[[content]]'
		), array(
			$templatePath,
			trim($user->name),
			$content
		), $template);

		// Prepare sending of email
		$hEmail->addTo(new Email_Address($user->email, $user->name.' '.$user->surname));
		$hEmail->setSubject($subject);
		$hEmail->setHtml($template);
		$hEmail->setFrom(new Email_Address('noreply@mpconsulting.co.za', 'Brighter Futures Tuition - please don\'t reply'));
		$hEmail->setReplyTo(new Email_Address('info@brighterfuture.co.za', 'Members Support'));
		$hEmail->attachFile($templatePath.'user-email/logo.png', 'Logo', true);
		foreach($attachments as $attachment) {
			$inline = isset($attachment['inline']) ? $attachment['inline'] : false;
			$hEmail->attachFile($attachment['path'], $attachment['name'], $inline);
		}
		$result = $hEmail->send();

		// Add a log entry
		if($result) {
			$connection = Application::GetApplication()->getConnection();
			$connection->insert('email_logs', array(
				'date_created' => gmdate('Y-m-d H:i:s'),
				'date_modified' => gmdate('Y-m-d H:i:s'),
				'status' => 'PENDING',
				'status_date' => gmdate('Y-m-d H:i:s'),
				'user_id' => $user->id,
				'uid' => $hEmail->getLastMessageID(),
				'to' => $user->name.' '.$user->surname.' <'.$user->email.'>',
				'subject' => $subject,
				'content' => $template
			));
		}

		// Return the result
		return $result ? true : false;
	}

	function sendQuotationEmail($email, $name, $subject, $content, $attachments = array()) {
		require_once(SYSTEM_PATH.'helpers/email.inc.php');
		$hEmail = new Email_Helper();

		// Load and prepare the template
		$templatePath = APPLICATION_PATH.'templates/';
		$template = file_get_contents($templatePath.'quotation.html');
		$template = str_replace(array(
			'[[path]]',
			'[[name]]',
			'[[content]]'
		), array(
			$templatePath,
			$name,
			$content
		), $template);

		// Prepare sending of email
		$hEmail->addTo(new Email_Address($email, $name));
		$hEmail->setSubject($subject);
		$hEmail->setHtml($template);
		$hEmail->setFrom(new Email_Address('noreply@oqm.co.za', 'OQM Freelancing - please don\'t reply'));
		$hEmail->setReplyTo(new Email_Address('admin@oqm.co.za', 'Quotation Request'));
		$hEmail->attachFile($templatePath.'quotation/logo.png', 'Logo', true);
		foreach($attachments as $attachment) {
			$inline = isset($attachment['inline']) ? $attachment['inline'] : false;
			$hEmail->attachFile($attachment['path'], $attachment['name'], $inline);
		}
		$result = $hEmail->send();

		// Add a log entry
		/*if($result) {
			$connection = Application::GetApplication()->getConnection();
			$connection->insert('email_logs', array(
				'date_created' => gmdate('Y-m-d H:i:s'),
				'date_modified' => gmdate('Y-m-d H:i:s'),
				'status' => 'PENDING',
				'status_date' => gmdate('Y-m-d H:i:s'),
				'user_id' => 0,
				'uid' => $hEmail->getLastMessageID(),
				'to' =>$name.' <'.$email.'>',
				'subject' => $subject,
				'content' => $template
			));
		}*/

		// Return the result
		return $result ? true : false;
	}
	
	function sendGuestEmail($email, $name, $subject, $content, $attachments = array()) {
		require_once(SYSTEM_PATH.'helpers/email.inc.php');
		$hEmail = new Email_Helper();
	
		// Load and prepare the template
		$templatePath = APPLICATION_PATH.'templates/';
		$template = file_get_contents($templatePath.'user-email.html');
		$template = str_replace(array(
				'[[path]]',
				'[[name]]',
				'[[content]]'
		), array(
				$templatePath,
				$name,
				$content
		), $template);
	
		// Prepare sending of email
		$hEmail->addTo(new Email_Address($email, $name));
		$hEmail->setSubject($subject);
		$hEmail->setHtml($template);
		$hEmail->setFrom(new Email_Address('noreply@developersjunction.co.za', 'Developers Junction - please don\'t reply'));
		$hEmail->setReplyTo(new Email_Address('info@developersjunction.co.za', 'Contact Form'));
		$hEmail->attachFile($templatePath.'quotation/logo.png', 'Logo', true);
		foreach($attachments as $attachment) {
			$inline = isset($attachment['inline']) ? $attachment['inline'] : false;
			$hEmail->attachFile($attachment['path'], $attachment['name'], $inline);
		}
		$result = $hEmail->send();
	
		// Add a log entry
		if($result) {
			$connection = Application::GetApplication()->getConnection();
			$connection->insert('email_logs', array(
					'date_created' => gmdate('Y-m-d H:i:s'),
					'date_modified' => gmdate('Y-m-d H:i:s'),
					'status' => 'PENDING',
					'status_date' => gmdate('Y-m-d H:i:s'),
					'user_id' => 0,
					'uid' => $hEmail->getLastMessageID(),
					'to' =>$name.' <'.$email.'>',
					'subject' => $subject,
					'content' => $template
			));
		}
	
		// Return the result
		return $result ? true : false;
	}
	
	function convertTimeToLocal($UTCdate, $format = "Y-m-d H:i:s") {
		$time = strtotime($UTCdate.' UTC');
		$dateInLocal = date($format, $time);
		return $dateInLocal;
	}

	function wrapHtmlTag($string, $tag = "p", $attributes = array()) {
		$attr = "";
		if($attributes) {
			foreach($attributes as $key => $value) {
				$attr .= " ".$key."='".$value."'";
			}
		}
		return "<".$tag.$attr.">".$string."</".$tag.">";
	}

	/*function imageBase64($imageData, $mimeType) {
		return "<img src='".$."' />"
	}*/

	function sendSms($msg, $user_id, $number) {
		$this->loadModel('Bulk-SMS');
		$this->mBulk_SMS->sendSingleSMS($msg, $user_id, $number);
	}

	define('YEAR_START_DATE', '2014-04-01');
