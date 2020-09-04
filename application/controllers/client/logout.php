<?php	
class Client_Logout_Controller extends Controller {
	public function indexAction() {
		// Kill session
		unset($_SESSION['user']);
		
		// TODO: Add log entry
		
		// Redirect back to home page
		header('Status: 200');
		header('Location: /');
		die();
	}
}
