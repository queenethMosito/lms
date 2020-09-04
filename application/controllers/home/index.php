<?php
	class Home_Index_Controller extends Controller {	
	    public function __construct() {
	        parent::__construct();
	        
	        // If already logged in, then redirect to home page
	        if(isset($_SESSION['user']) && $_SESSION['user']['id']) {
	            header('Status: 200');
	            header('Location: /');
	            die();
	        }
	        
	        // Make sure its SSL
	       $this->forceSSL();
	    }
		public function indexAction() {
		
			$this->meta ['title'] = 'Learner Management System';
			
			echo $this->loadView('public/home');
		}
			
		
		public function loginAction(){
		    $this->meta ['title'] = 'Learner Management Login';
		    $data = array();
		    $data['error'] = null;
		    $data['email'] = '';
		    $data['showBackButton'] = true;
		    if(isset($_POST['login'])) {
		        $email = trim($this->hInput->post('email'));
		        $password = $this->hInput->post('password');
		        $result = $this->mUser->validateAndLogin($email, $password);
		        $data['error'] = $result->message;
		        $data['result'] = $result;
		        $data['email'] = $email;
		    }
		    echo $this->loadView('login/login',$data);
		}
		
	}
