<?php
class Client_Classes_Controller extends Controller {

	public function __construct() {
		parent::__construct();
		$this->loadModel(array('Classes'));
		$this->loadModel(array('Attendees'));
		$this->loadModel(array('Attendance'));
	}

	public function indexAction() {
		$this->meta['title'] = 'Class Management';
		$user = $this->mUser->currentUser();
		$this->js_list[]="/themes/lms/js/moment.js";
		$this->js_list[]="/themes/lms/js/bootstrap-datetimepicker.min.js";
		
		$this->css_list[]="/themes/lms/css/bootstrap-datetimepicker.min.css";
		$this->js_list[]="/themes/lms//js/bootbox.min.js";
		$this->js_list[]="/themes/lms//js/moment-with-locales.js";
		
        //get list of active classes
		$classes = $this->mClasses->allClasses();
		
		$this->loadHelper('Html');
		echo $this->loadView('classes/classes', array(
		    'classes' => $classes
		));
	}



	public function submitclassesAction(){
		$errors = array();
		
		$json = $this->hInput->post('classes');
		try{
		    $this->mClasses->updateClasses(json_decode($json, true));
		}
		catch(Exception $e){
			$errors[] = $e->getMessage();
		}

		if(sizeof($errors) > 0){
			$errormessage = '';
			foreach($errors as $error){
				$errormessage .= "</p>".$error."</p>";
			}
			die(json_encode(array(
					'result' => 'success',
					'message' => $errormessage
			)));
		}

		die(json_encode(array(
				'result' => 'success',
				'message' => '<p>Class Updated</p>'
		)));
	}
	public function deleteclassesAction(){
	    $errors = array();
	    $class_id = $this->hInput->post('class_id');
	    try {
	        $this->mClasses->deleteClasses( json_decode ( $class_id, true ) );
	    } catch ( Exception $e ) {
	        $errors [] = $e->getMessage ();
	    }
	    if(sizeof($errors) > 0){
	        $errormessage = '';
	        foreach($errors as $error){
	            $errormessage .= "</p>".$error."</p>";
	        }
	        die(json_encode(array(
	            'result' => 'success',
	            'message' => $errormessage
	        )));
	    }
	    
	    die(json_encode(array(
	        'result' => 'success',
	        'message' => '<p>Class Deleted</p>'
	    )));
	}
	public function viewclasslistAction(){
	    
	    $classes = $this->mClasses->allClasses();
	    die($this->loadView('classes/view-class-list', array(
	        'classes' => $classes
	    )));
	}
	public function learnersAction($class_id){
	    
	    $this->js_list[]="/themes/lms/js/bootbox.min.js";
	    $this->js_list[]="/themes/lms/js/moment-with-locales.js";
	    $this->meta['title'] = 'Management Class List';
	    $classInfo = $this->mClasses->getClass($class_id);
	    $enrolledLearners = $this->mAttendance->getAttendanceLearners($class_id);
	    $enrolledStudents = [] ;
	    if(count($enrolledLearners)>0){
	        foreach ($enrolledLearners as $learner)
	        {
	            $enrolledStudents[] = $learner->attendee_id;
	        }
	    }
	     $allStudents = $this->mAttendees->getAllStudents();
	    echo $this->loadView('classes/learners', array(
	        'classInfo' => $classInfo,
	        'enrolledStudents' =>$enrolledStudents,
	        'allStudents' => $allStudents
	    ));
	 }
	 public function addClassListAction(){
	     $errors = array();
	     $learners = $this->hInput->post('learners');
	     try {
	         $this->mAttendance->updateAttendance( json_decode ( $learners, true ) );
	     } catch ( Exception $e ) {
	         $errors [] = $e->getMessage ();
	     }
	     if(sizeof($errors) > 0){
	         $errormessage = '';
	         foreach($errors as $error){
	             $errormessage .= "</p>".$error."</p>";
	         }
	         die(json_encode(array(
	             'result' => 'success',
	             'message' => $errormessage
	         )));
	     }
	     
	     die(json_encode(array(
	         'result' => 'success'
	     )));
	 }
	 public function attendanceAction($class_id)
	 {
	     $this->js_list[]="/themes/lms/js/bootbox.min.js";
	     $this->js_list[]="/themes/lms/js/moment-with-locales.js";
	     $this->meta['title'] = 'Management Class Attendance';
	     $classInfo = $this->mClasses->getClass($class_id);
	     $attendance = $this->mAttendance->getAttendanceLearnersWithFines($class_id);
	    
	     
	     echo $this->loadView('classes/attendance', array(
	         'classInfo' => $classInfo,
	         'attendance' =>$attendance,
	     ));
	 }
	 public function addRegisterAction(){
	     $errors = array();
	     $form = $this->hInput->post('attendance');
	     try {
	         $this->mAttendance->addClassAttendance( $form);
	     } catch ( Exception $e ) {
	         $errors [] = $e->getMessage ();
	     }
	     if(sizeof($errors) > 0){
	         $errormessage = '';
	         foreach($errors as $error){
	             $errormessage .= "</p>".$error."</p>";
	         }
	         die(json_encode(array(
	             'result' => 'success',
	             'message' => $errormessage
	         )));
	     }
	     
	     die(json_encode(array(
	         'result' => 'success',
	         'message' => '<p>Class Deleted</p>'
	     )));
	 }
	 public function fees_listAction($class_id)
	 {
	     $this->meta['title'] = 'Learner Fines';
	     $this->css_list[] = "/themes/lms/css/dataTables.bootstrap.css";
	     $this->js_list[] = "/themes/lms/js/jquery-datatable/jquery.dataTables.js";
	     $this->js_list[] = "/themes/lms/js/jquery-datatable/extensions/export/dataTables.buttons.min.js";
	     $this->js_list[] = "/themes/lms/js/jquery-datatable/extensions/export/buttons.flash.min.js";
	     $this->js_list[] = "/themes/lms/js/jquery-datatable/extensions/export/pdfmake.min.js";
	     $this->js_list[] = "/themes/lms/js/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js";
	     $this->js_list[] = "/themes/lms/js/jquery-datatable/extensions/export/dataTables.buttons.min.js";
	     $fines = $this->mAttendance->getAttendanceFines($class_id);
	     echo $this->loadView('classes/fees', array(
	         'fines' => $fines
	     ));
	 }
	 public function pass_listAction($class_id)
	 {
	     $this->meta['title'] = 'Learner Pass List';
	     $this->css_list[] = "/themes/lms/css/dataTables.bootstrap.css";
	     $this->js_list[] = "/themes/lms/js/jquery-datatable/jquery.dataTables.js";
	     $this->js_list[] = "/themes/lms/js/jquery-datatable/extensions/export/dataTables.buttons.min.js";
	     $this->js_list[] = "/themes/lms/js/jquery-datatable/extensions/export/buttons.flash.min.js";
	     $this->js_list[] = "/themes/lms/js/jquery-datatable/extensions/export/pdfmake.min.js";
	     $this->js_list[] = "/themes/lms/js/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js";
	     $this->js_list[] = "/themes/lms/js/jquery-datatable/extensions/export/dataTables.buttons.min.js";
	     $fines = $this->mAttendance->getAttendancePass($class_id);
	     echo $this->loadView('classes/passList', array(
	         'fines' => $fines
	     ));
	 }
}
