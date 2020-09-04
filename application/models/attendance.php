<?php
class Attendance_Model extends Model {
    public function __construct() {
        parent::__construct('attendance');
    }
    public function getStudentClassHistory($attendee_id)
    {
            $sql = "SELECT c.*
                    FROM `classes` c
                    LEFT JOIN `attendance` a ON c.class_id=a.class_id
                    WHERE a.`attendee_id` = ". $attendee_id;
        
            $results = $this->connection->query($sql)->rows;
            if(count($results)>0)
            {
                foreach ($results as $result) {
                    
                    $result->attendance = $this->connection->querySingleRow("SELECT * FROM `attendance` WHERE `class_id` =" .$result->class_id . " AND `attendee_id` = ".$attendee_id);
                    if(count($result->attendance)>0)
                    {
                        $result->attendance->fines = $this->connection->querySingleRow("SELECT * FROM `fines` WHERE `attendance_id` =" .$result->attendance->attendance_id);
                        
                    }
                }
            }
            return $results;
    }
    public function getAttendanceLearnersWithFines($class_id)
    {
        $sql = "SELECT * 
FROM `attendance` a 
LEFT JOIN `attendees` att ON a.`attendee_id` = att.`attendee_id`
WHERE a.`class_id` =". $class_id;
        $results = $this->connection->query($sql)->rows;
        if(count($results)>0)
        {
            foreach ($results as $result) {
                
                $result->fines = $this->connection->querySingleRow("SELECT * FROM `fines` WHERE `attendance_id` =" .$result->attendance_id);
            }
        }
        return $results;
        
    }
    public function getAttendanceLearners($class_id)
    {
        $sql = "SELECT * FROM `attendance` WHERE `class_id` =". $class_id;
        $results = $this->connection->query($sql)->rows;
        return $results;
    }
    public function updateAttendance($learners)
    {
        $this->connection->query('delete from attendance where class_id = '.$learners['class_id']);
        foreach($learners['studentIDs'] as $students)
        {
            $this->connection->insert('attendance', array(
                'class_id'=>$learners['class_id'],
                'attendee_id'=>$students
            ));
        }
       
    }
    public function addClassAttendance($attendance)
    {
        
        if(count($attendance)>0)
        {
            foreach($attendance as $attend)
            {
                if(isset($attend['attendance_id']))
                {
                    $register = [];
                    $register['attendance_id'] = $attend['attendance_id'];
                    $register['required'] = isset($attend['required']) ? $attend['required'] : 0;
                    $register['attended'] = isset($attend['attendance']) && $attend['attendance']==1 ? 1 : 0;
                    $register['communicated'] = isset($attend['attendance']) && $attend['attendance']==0 ? 1 : 0;
                    $this->connection->update("attendance", $register);
                   
                    if($register['communicated']==1)
                    {
                        if(isset($attend['fines']))
                        {
                            $sql = "SELECT * FROM `fines` WHERE `attendance_id` =". $register['attendance_id'];
                            $results = $this->connection->querySingleRow($sql);
                            if(count($results)>0)
                            {
                                $fines = [];
                                $fines['fine_id'] = $results->fine_id;
                                $fines['fine_date'] = date("Y-m-d H:i:s");
                                $fines['fine_amount'] = $attend['fines'];
                                $this->connection->update("fines", $fines);
                            }
                            else {
                                $fines = [];
                                $fines['attendance_id'] = $register['attendance_id'];
                                $fines['fine_date'] = date("Y-m-d H:i:s");
                                $fines['fine_amount'] = $attend['fines'];
                                $this->connection->insert("fines", $fines);
                            }
                        }
                    }
                    
                }
              }
        }
       
    }
    public function getAttendanceFines($class_id)
    {
        $sql = "SELECT *
        FROM `attendance` a
        LEFT JOIN `attendees` att ON a.`attendee_id` = att.`attendee_id`
        INNER JOIN `fines` f ON a.`attendance_id` = f.`attendance_id`
        WHERE a.`class_id`=". $class_id;
        $results = $this->connection->query($sql)->rows;
        return $results;
    }
    public function getAllAttendanceFines()
    {
        $sql = "SELECT SUM(f.`fine_amount`) AS fines
        FROM `attendance` a
        LEFT JOIN `attendees` att ON a.`attendee_id` = att.`attendee_id`
        INNER JOIN `fines` f ON a.`attendance_id` = f.`attendance_id`";
        $results = $this->connection->querySingleRow($sql)->fines;
        if($results == '')
            return 0;
        else
        return $results;
    }
    public function geAllAttendancePass()
    {
        $sql = "SELECT COUNT(*) AS countPassed
FROM `attendance` a
LEFT JOIN `attendees` att ON a.`attendee_id` = att.`attendee_id`
WHERE  a.`required`=1 AND a.`attended`=1";
        $results = $this->connection->querySingleRow($sql)->countPassed;
        return $results;
    }
    public function getAttendancePass($class_id)
    {
        $sql = "SELECT *
FROM `attendance` a
LEFT JOIN `attendees` att ON a.`attendee_id` = att.`attendee_id`
WHERE a.`class_id`=". $class_id . " AND a.`required`=1 AND a.`attended`=1";
        $results = $this->connection->query($sql)->rows;
        return $results;
    }
}