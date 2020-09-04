<?php
class Attendees_Model extends Model {
    public function __construct() {
        parent::__construct('attendees');
    }
    public function getLimitedStudents($term)
    {
        
        $ret = array();
        $sql = "SELECT DISTINCT(a.attendee_id),a.student_number,a.email_address, CONCAT(a.first_name, ' ', a.last_name) AS fullname
              FROM `attendees` a
              WHERE a.status_id=1 AND CONCAT(a.first_name,' ', a.last_name, ' ' , a.email_address,' ', a.student_number) LIKE '%" . $term . "%'
              LIMIT 50";
        $results = $this->connection->query($sql)->rows;
        
        return $results;
    }
    public function getCountStudents($term)
    {
        
        $ret = array();
        $sql = "SELECT DISTINCT(a.attendee_id)
                FROM `attendees` a
                WHERE a.status_id=1 AND CONCAT(a.first_name,' ', a.last_name, ' ' , a.email_address,' ', a.student_number) LIKE '%" . $term . "%'
                ";
        $results = $this->connection->query($sql)->rowCount;
        
        return $results;
    }
    public function updateLearners($learner){
        if(isset($learner['attendee_id']) && $learner['attendee_id'] != null && trim($learner['attendee_id']) != ''){
            $this->connection->update("attendees", $learner);
        }
        else{
            
            $student_number = $this->connection->querySingleRow("select fnGET_MAX_STUDENT_NR() as student_number")->student_number;
            $learner['student_number'] = $student_number;
            $learner['status_id'] = 1;
            $this->connection->insert("attendees", $learner);
        }
    }
    public function getFullStudent($attendee_id)
    {
        $sql = "SELECT *
        FROM attendees 
        WHERE attendee_id = " . $attendee_id;
        $student = (array) $this->connection->querySingleRow($sql);
        foreach ($student as $key => $value) {
            $student{$key} = $value;
        }
        return (object) $student;
    }
    public function getAllStudents()
    {
        $sql = "SELECT * FROM attendees WHERE status_id = 1";
        $results = $this->connection->query($sql)->rows;
        return $results;
    }
}