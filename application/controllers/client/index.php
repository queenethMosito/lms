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
	public function leadManagementAction()
	{
	    $data=array();
	   
	    $data=$this->mLeads->getAllLeads();
	    echo $this->loadView('admin/management',$data);
	}
	public function saveLeadAction()
	{
	    $errors = array ();
	    /*if (! $this->mUser->accessControl ()) {
	     return;
	     }*/
	    $json = $this->hInput->post ( 'lead' );
	    try {
	        
	        $lead=json_decode ( $json, true );
	        $duplicate=$this->checkDuplicates($lead);
	        if(!isset($lead['leads_detail_id']) && $lead['leads_detail_id'] == null && trim($lead['leads_detail_id']) == '' &&count($duplicate)>0)
	        {
	            $errors [] = 'The system has picked up that there already a lead saved
							with the e-mail address that you have supplied. The system does not allow
							multiple leads with the same e-mail address.';
	            
	        }
	        else {
	            $this->mLeads->updatelead ($lead );
	        }
	        
	        
	    } catch ( Exception $e ) {
	        $errors [] = $e->getMessage ();
	    }
	    
	    if (sizeof ( $errors ) > 0) {
	        $errormessage = '';
	        foreach ( $errors as $error ) {
	            $errormessage .= "<p>" . $error . "</p>";
	        }
	        die ( json_encode ( array (
	            'result' => 'error',
	            'message' => $errormessage
	        ) ) );
	    }
	    
	    die ( json_encode ( array (
	        'result' => 'success',
	        'message' => '<p>Leads list has been updated</p>'
	    ) ) );
	}


	public function loadLeadsAction(){
	    $json = $this->hInput->get ( 'filter' );
	    $filters=json_decode ( $json, true );
	  
	    $results=$this->mLeads->getlead ( $filters);
	    die ( json_encode ( array (
	        'result' => $results
	    ) ) );
	}
	public function deleteLeadAction()
	{
	    $id = $this->hInput->post ( 'id' );
	    $lead=array('leads_detail_id'=>$id,
	        'status_id'=>0
	    );
	    $this->connection->update("leads_details", $lead);
	    die();
	}
	    
	public function checkDuplicates($fields)
	{
	    $sql="select leads_detail_id from leads_details where status_id=1 and email_address='".$fields['email_address']."'";
	   return $this->connection->querySingleRow($sql);
	   
	}
	public function getSessionsAction()
	{
	    $classes = $this->mClasses->allClasses();
	    die(json_encode($classes));
	}
}