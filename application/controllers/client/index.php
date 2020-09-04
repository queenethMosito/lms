<?php
class Client_Index_Controller extends Controller {
    public function __construct() {
        parent::__construct();
        $this->loadModel(array('Classes'));
        $this->loadModel(array('Attendance'));
        
    }
 
	public function indexAction() {
	   $data=[];
	   $this->meta ['title'] = 'Learner Management System';
	   $this->js_list[]="/themes/lms/js/moment.js";
	   $this->js_list[]="/themes/lms/js/fullcalendar.min.js";
	   $this->css_list[]="/themes/lms/css/fullcalendar.css";
	   $pass = $this->mAttendance->geAllAttendancePass();
	   $fines = $this->mAttendance->getAllAttendanceFines();
	   echo $this->loadView('admin/main',array(
	       'pass' => $pass,
	       'fines' => $fines
	   ));

	}
	
	public function getSessionsAction()
	{
	    $classes = $this->mClasses->allClasses();
	    die(json_encode($classes));
	}
}