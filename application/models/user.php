<?php
class User_Model extends Model {
	public function __construct() {
		parent::__construct('users');
	}
	
	public function currentUser() {
		$userID = isset($_SESSION['user']) && isset($_SESSION['user']['id']) ?
			(int) $_SESSION['user']['id'] : 0;
		if($userID) {
			$user = $this->selectSingle($userID);
			
			// Load some permissions based on type
			/*if($user) {
				switch($user->type) {
					case 1: $user->perms = $this->getAdminPerms($user->id); break;
					case 2: $user->perms = $this->getFacultyPerms($user->id); break;
					default: $user->perms = null; break;
				}
			}*/
			
			// Return the user
			return $user;
		}
		return null;
	}
	
	public function validateAndLogin($username, $password) {
		// First try to find a user with the specified username or email address
		$user = $this->findByEmail($username);
		
		if(!$user) {
			// TODO: Add log entry for failed login
			
			// Return the result
			return (object) array(
				'valid' => 0,
				'message' => 'Invalid username/e-mail address or password entered',
				'user' => null,
				'passwordError' => true
			);
		}
		
		// Do a password match attempt
		$hashPassword = hashPassword($user->salt, $password);
		if($hashPassword != $user->password) {
			// TODO: Add log entry for failed login
			
			// Return the result
			return (object) array(
				'valid' => 0,
				'message' => 'Invalid username/e-mail address or password entered',
				'user' => $user,
				'passwordError' => true
			);
		}
		
		// Check the status of the user
		$result = (object) array('valid' => 0, 'message' => '', 'user' => $user, 'passwordError' => false);
		if($user->account_active <= 0) {
			$result->message = '
				<h3>Account is deactivated</h3>
				<p>
					Your account has been deactivated.
				</p>';
			return $result;
		}
		
		// All good so log the user in
		$update = array();
		$update['user_id'] = $user->user_id;
		$update['date_last_login'] = gmdate('Y-m-d H:i:s');
		$update['last_login_ip'] = getIPAddress(); // TODO: Use helper
		$this->update($update);
		
		$_SESSION['user'] = array(
			'id' => $user->user_id,
			'email' => $user->email_address,
			'username' => $user->email_address,
			'login_time' => gmdate('Y-m-d H:i:s')
		);
		
		// TODO: Add log entry
		
		// Redirect to home page
		header('Status: 200');
		header('Location: /');
		
		die();
	}
	
	public function setPassword($userID, $password) {
		$userID = (int) $userID;
		$password = trim($password);
		$salt = md5(microtime());
		$hash = hashPassword($salt, $password);
		$this->connection->update('users', array(
			'user_id' => $userID,
			'salt' => $salt,
			'password' => $hash,
			'date_modified' => gmdate('Y-m-d H:i:s')
		));
	}
	
	public function findByUsername($username) {
		$usernameSql = trim($this->connection->prepareValue($username));
		$sql = 'SELECT * FROM users WHERE username = "'.$usernameSql.'"';
		$result = $this->connection->query($sql);
		
		return $result->rowCount > 0 ? $result->rows[0] : null;
	}
	
	public function findByEmail($email) {
		$emailSql = trim($this->connection->PrepareValue($email));
		$sql = 'SELECT * FROM users WHERE email_address = "'.$emailSql.'"';
		$result = $this->connection->query($sql);
		
		return $result->rowCount > 0 ? $result->rows[0] : null;
	}
	
	
}
