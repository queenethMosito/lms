<?php
class Client_Attendees_Controller extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->loadModel(array('Attendees'));
        $this->loadModel(array('Classes'));
        $this->loadModel(array('Attendance'));
    }
    public function indexAction()
    {
        $this->meta['title'] = 'Learner Management';
        $this->css_list[] = "/themes/lms/css/dataTables.bootstrap.css";
        $this->js_list[] = "/themes/lms/js/jquery-datatable/jquery.dataTables.js";
        $this->js_list[] = "/themes/lms/js/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js";
        $this->js_list[] = "/themes/lms/js/jquery-datatable/extensions/export/dataTables.buttons.min.js";
        echo $this->loadView('attendees/attendees', array(
            ));
    }
    public function searchAction()
    {
        $errors = array();
        $result = null;
        
        $query = $this->hInput->post('query');
        $studentCount = 0;
        try {
            
            $ret = $this->mAttendees->getLimitedStudents($query);
            $studentCount = $this->mAttendees->getCountStudents($query);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        if (sizeof($errors) > 0) {
            $errormessage = '';
            foreach ($errors as $error) {
                $errormessage .= "</p>" . $error . "</p>";
            }
            die(json_encode(array(
                'result' => 'error',
                'message' => $errormessage,
            )));
        }
        
        die(json_encode(array(
            'result' => $ret,
            'count' => $studentCount,
        )));
        
    }
    public function submitLearnerAction(){
        $errors = array();
        
        $json = $this->hInput->post('learner');
        try{
            $this->mAttendees->updateLearners(json_decode($json, true));
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
            'message' => '<p>Learner Updated</p>'
        )));
    }
    
    public function viewAction($attendee_id)
    {
        $this->js_list[]="/themes/lms/js/moment.js";
        $this->js_list[]="/themes/lms/js/fullcalendar.min.js";
        $this->css_list[]="/themes/lms/css/fullcalendar.css";
        $this->meta['title'] = 'Learner Information';
        $student = $this->mAttendees->getFullStudent($attendee_id);
        $student->class_history = $this->mAttendance->getStudentClassHistory($attendee_id);
       echo $this->loadView('attendees/view', array(
            'student' =>$student
            ));
            
    }
}