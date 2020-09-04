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

	
	define('YEAR_START_DATE', '2014-04-01');
